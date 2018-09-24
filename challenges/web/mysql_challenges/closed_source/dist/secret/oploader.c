#include "oploader.h"
#include "php.h"
#include "zend_execute.h"
#include "secret.h"

ZEND_DECLARE_MODULE_GLOBALS(secret)

void load_oparray(FILE* fp, zend_op_array** oparray)
{
    char addr;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *oparray = emalloc(sizeof(zend_op_array));
        fload(&((*oparray)->type), sizeof((*oparray)->type), 1, fp);
        fload(&((*oparray)->arg_flags), sizeof((*oparray)->arg_flags[0]), 3, fp);
        fload(&((*oparray)->fn_flags), sizeof((*oparray)->fn_flags), 1, fp);
        load_zend_string(fp, &((*oparray)->function_name));
        load_class_entry(fp, &((*oparray)->scope));
        load_zend_function(fp, &((*oparray)->prototype));
        fload(&((*oparray)->num_args), sizeof((*oparray)->num_args), 1, fp);
        fload(&((*oparray)->required_num_args), sizeof((*oparray)->required_num_args), 1, fp);
        load_arg_info(fp, &((*oparray)->arg_info));
        fload(&((*oparray)->this_var), sizeof((*oparray)->this_var), 1, fp);
        fload(&((*oparray)->last), sizeof((*oparray)->last), 1, fp);
        load_opcodes(fp, &((*oparray)->opcodes), (*oparray)->last);
        fload(&((*oparray)->last_var), sizeof((*oparray)->last_var), 1, fp);
        fload(&((*oparray)->T), sizeof((*oparray)->T), 1, fp);
        load_vars(fp, &((*oparray)->vars), (*oparray)->last_var);
        fload(&((*oparray)->last_brk_cont), sizeof((*oparray)->last_brk_cont), 1, fp);
        fload(&((*oparray)->last_try_catch), sizeof((*oparray)->last_try_catch), 1, fp);
        load_brk_cont_array(fp, &((*oparray)->brk_cont_array), (*oparray)->last_brk_cont);
        load_try_catch_array(fp, &((*oparray)->try_catch_array), (*oparray)->last_try_catch);
        load_hashtable(fp, &((*oparray)->static_variables));
        load_zend_string(fp, &((*oparray)->filename));
        fload(&((*oparray)->line_start), sizeof((*oparray)->line_start), 1, fp);
        fload(&((*oparray)->line_end), sizeof((*oparray)->line_end), 1, fp);
        load_zend_string(fp, &((*oparray)->doc_comment));
        fload(&((*oparray)->early_binding), sizeof((*oparray)->early_binding), 1, fp);
        fload(&((*oparray)->last_literal), sizeof((*oparray)->last_literal), 1, fp);
        load_literals(fp, &((*oparray)->literals), (*oparray)->last_literal);
        fload(&((*oparray)->cache_size), sizeof((*oparray)->cache_size), 1, fp);
        load_runtime_cache(fp, &((*oparray)->run_time_cache), (*oparray)->cache_size);
    } else {
        *oparray = NULL;
    }
}

void load_opcodes(FILE* fp, zend_op** opcodes, uint32_t count)
{
    uint32_t i;
    char addr;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *opcodes = emalloc(sizeof(zend_op)*count);
        for (i = 0; i < count; i++) {
            load_zend_op(fp, &((*opcodes)[i]));
        }
    } else {
        *opcodes = NULL;
    }
}

void load_zend_op(FILE* fp, zend_op* op)
{
    intptr_t offset;

    fload(&offset, sizeof(offset), 1, fp);
    op->handler = get_handler_by_offset(offset);
    load_znode_op(fp, &(op->op1), &(op->op1_type));
    load_znode_op(fp, &(op->op2), &(op->op2_type));
    load_znode_op(fp, &(op->result), &(op->result_type));
    fload(&(op->extended_value), sizeof(op->extended_value), 1, fp);
    fload(&(op->lineno), sizeof(op->lineno), 1, fp);
    fload(&(op->opcode), sizeof(op->opcode), 1, fp);
    op->opcode = 0;
}

void load_znode_op(FILE* fp, znode_op* node, zend_uchar* type)
{
    fload(type, sizeof(*type), 1, fp);
    switch(*type) {
        case IS_UNDEF:
        case IS_UNUSED:
            fload(&(node->var), sizeof(node->var), 1, fp);
            break;
        case IS_CONST:
        case IS_VAR:
        case IS_TMP_VAR:
        case IS_CV:
        case 36:
            fload(&(node->var), sizeof(node->var), 1, fp);
            break;
        default:
            debug("ERROR: %d\n", type);
    }
}

void load_literals(FILE* fp, zval** literals, int count)
{
    char addr;
    int i;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *literals = emalloc(sizeof(zval) * count);
        for (i = 0; i < count; i++) {
            load_zval(fp, &((*literals)[i]));
        }
    } else {
        *literals = NULL;
    }
}

void load_zval(FILE* fp, zval* val)
{
    fload(&(val->u1), sizeof(val->u1), 1, fp);
    fload(&(val->u2), sizeof(val->u2), 1, fp);
    switch(Z_TYPE_P(val)) {
        case IS_UNDEF:
        case IS_NULL:
        case IS_TRUE:
        case IS_FALSE:
            break;
        case IS_LONG:
            fload(&(val->value.lval), sizeof(val->value.lval), 1, fp);
            break;
        case IS_DOUBLE:
            fload(&(val->value.dval), sizeof(val->value.dval), 1, fp);
            break;
        case IS_STRING:
            load_zend_string(fp, &(val->value.str));
            break;
        case IS_ARRAY:
            load_hashtable(fp, &(val->value.arr));
            break;
        case IS_REFERENCE:
        case IS_CONSTANT:
        case IS_CONSTANT_AST:
        case IS_INDIRECT:
        case IS_PTR:
            load_zend_function(fp, (zend_function**)(&(val->value.ptr)));
            break;
        default:
            debug("ZVAL type not implemented yet: %d\n.", Z_TYPE_P(val));
    }
}

void load_vars(FILE* fp, zend_string*** vars, int count)
{
    char addr;
    int i;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *vars = emalloc(sizeof(zend_string*) * count);
        for (i = 0; i < count; i++) {
            load_zend_string(fp, &((*vars)[i]));
        }
    } else {
        *vars = NULL;
    }
}

void load_zend_string(FILE* fp, zend_string** str)
{
    char addr = 0;
    size_t len;
    char* buffer;
    int i;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        fload(&len, sizeof(len), 1, fp);
        buffer = emalloc(sizeof(char)*len);
        fload(buffer, sizeof(char), len, fp);

        for (i = 0; i < len; i++) {
            buffer[i] ^= 0x80;   
        }
        *str = zend_string_init(buffer, len, 0);
        efree(buffer);
    } else {
        *str = NULL;
    }
}

void load_class_entry(FILE* fp, zend_class_entry** ce)
{
    char addr = 0;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        debug("NON-NULL class entry!");
    } else {
        *ce = NULL;
    }
}

void load_zend_function(FILE* fp, zend_function** func)
{
    char addr = 0;
    zend_op_array* oparray;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *func = emalloc(sizeof(zend_function));
        fload(&((*func)->type), sizeof((*func)->type), 1, fp);
        switch ((*func)->type) {
            case 1:
                // internal function
                break;
            case 2:
                // php function
                load_oparray(fp, &oparray);
                if (oparray != NULL) {
                    (*func)->op_array = *oparray;
                }
                break;
            default:
                php_printf("Unknown function type: %d.\n", (*func)->type);
        }
    } else {
        *func = NULL;
    }
}

void load_arg_info(FILE* fp, zend_arg_info** info)
{
    char addr = 0;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        debug("NON_NULL ZEND_ARG_INFO\n");
    } else {
        *info = NULL;
    }
}

void load_brk_cont_array(FILE* fp, zend_brk_cont_element** array, int count)
{
    char addr = 0;
    int i;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        *array = emalloc(sizeof(zend_brk_cont_element) * count);
        for (i = 0; i < count; i++) {
            load_brk_cont_element(fp, &((*array)[i]));
        }
    } else {
        *array = NULL;
    }
}

void load_brk_cont_element(FILE* fp, zend_brk_cont_element* elem)
{
    fload(&(elem->start), sizeof(elem->start), 1, fp);
    fload(&(elem->cont), sizeof(elem->cont), 1, fp);
    fload(&(elem->brk), sizeof(elem->brk), 1, fp);
    fload(&(elem->parent), sizeof(elem->parent), 1, fp);
}

void load_try_catch_array(FILE* fp, zend_try_catch_element** array, int count)
{
    char addr = 0;
    int i;

    fload(&(addr), sizeof(addr), 1, fp);
    if (addr) {
        *array = emalloc(sizeof(zend_try_catch_element) * count);
        for (i = 0; i < count; i++) {
            load_try_catch_element(fp, &((*array)[i]));
        }
    } else {
        *array = NULL;
    }
}

void load_try_catch_element(FILE* fp, zend_try_catch_element* elem)
{
    fload(&(elem->try_op), sizeof(elem->try_op), 1, fp);
    fload(&(elem->catch_op), sizeof(elem->catch_op), 1, fp);
    fload(&(elem->finally_op), sizeof(elem->finally_op), 1, fp);
    fload(&(elem->finally_end), sizeof(elem->finally_end), 1, fp);
}

void load_bucket(FILE* fp, Bucket* bucket)
{
    fload(&(bucket->h), sizeof(bucket->h), 1, fp);
    load_zend_string(fp, &(bucket->key));
    load_zval(fp, &(bucket->val));
}

void load_hashtable(FILE* fp, HashTable** table)
{
    uint32_t i;
    uint32_t table_size = 0;
    char addr = 0;

    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        ALLOC_HASHTABLE(*table);
        fload(&table_size, sizeof(table_size), 1, fp);

        (*table)->nTableSize = table_size;
        fload(&((*table)->nTableMask), sizeof((*table)->nTableMask), 1, fp);
        fload(&((*table)->nNumUsed), sizeof((*table)->nNumUsed), 1, fp);
        fload(&((*table)->nNumOfElements), sizeof((*table)->nNumOfElements), 1, fp);
        fload(&((*table)->nNextFreeElement), sizeof((*table)->nNextFreeElement), 1, fp);
        fload(&((*table)->u), sizeof((*table)->u), 1, fp);
        fload(&((*table)->nInternalPointer), sizeof((*table)->nInternalPointer), 1, fp);
        (*table)->pDestructor = ZVAL_PTR_DTOR;

        (*table)->arData = emalloc(sizeof(Bucket)*table_size);
        for (i = 0; i < (*table)->nNumUsed; ++i) {
            load_bucket(fp, &((*table)->arData[i]));
        }
    } else {
        *table = NULL;
    }
}

void load_runtime_cache(FILE* fp, void*** cache, int count)
{
    char addr = 0;
    fload(&addr, sizeof(addr), 1, fp);
    if (addr) {
        debug("load_runtime_cache NOT DONE: %d.\n", count);
    } else {
        *cache = NULL;
    }
}

void* get_handler_by_offset(intptr_t offset TSRMLS_DC)
{
    const void* base_handler;
    zval str;

    if (SECRET_G(handlers_offset) == 0) {
        ZVAL_STRING(&str, "echo 1;");
        base_handler = zend_compile_string(&str, "")->opcodes[0].handler;
        SECRET_G(handlers_offset) = (intptr_t)base_handler;
    }

    return (void*)(SECRET_G(handlers_offset) + offset);
}

void fload(void* dest, size_t size, size_t n, FILE* fp)
{
    size_t result = fread(dest, size, n, fp);
    if (result != n) {
        debug("fload error\n");
    }
}

void debug(const char* fmt, ...)
{
    va_list args;
    va_start(args, fmt);
    php_printf(fmt, args);
    va_end(args);
}