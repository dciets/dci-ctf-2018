#include "opdumper.h"
#include "php.h"
#include "zend_execute.h"
#include "secret.h"

ZEND_DECLARE_MODULE_GLOBALS(secret)

void write_addr(void* addr, FILE* fp)
{
    if (addr) {
        fputc(1, fp);
    } else {
        fputc(0, fp);
    }
}

void dump_oparray(FILE* fp, zend_op_array* oparray)
{
    write_addr(oparray, fp);
    if (oparray != NULL) {
        fwrite(&(oparray->type), sizeof(oparray->type), 1, fp);
        fwrite(&(oparray->arg_flags), sizeof(oparray->arg_flags[0]), 3, fp);
        fwrite(&(oparray->fn_flags), sizeof(oparray->fn_flags), 1, fp);
        dump_zend_string(fp, oparray->function_name);
        dump_class_entry(fp, oparray->scope);
        dump_zend_function(fp, oparray->prototype);
        fwrite(&(oparray->num_args), sizeof(oparray->num_args), 1, fp);
        fwrite(&(oparray->required_num_args), sizeof(oparray->required_num_args), 1, fp);
        dump_arg_info(fp, oparray->arg_info);
        fwrite(&(oparray->this_var), sizeof(oparray->this_var), 1, fp);
        fwrite(&(oparray->last), sizeof(oparray->last), 1, fp);
        dump_opcodes(fp, oparray->opcodes, oparray->last);
        fwrite(&(oparray->last_var), sizeof(oparray->last_var), 1, fp);
        fwrite(&(oparray->T), sizeof(oparray->T), 1, fp);
        dump_vars(fp, oparray->vars, oparray->last_var);
        fwrite(&(oparray->last_brk_cont), sizeof(oparray->last_brk_cont), 1, fp);
        fwrite(&(oparray->last_try_catch), sizeof(oparray->last_try_catch), 1, fp);
        dump_brk_cont_array(fp, oparray->brk_cont_array, oparray->last_brk_cont);
        dump_try_catch_array(fp, oparray->try_catch_array, oparray->last_try_catch);
        dump_hashtable(fp, oparray->static_variables);
        dump_zend_string(fp, oparray->filename);
        fwrite(&(oparray->line_start), sizeof(oparray->line_start), 1, fp);
        fwrite(&(oparray->line_end), sizeof(oparray->line_end), 1, fp);
        dump_zend_string(fp, oparray->doc_comment);
        fwrite(&(oparray->early_binding), sizeof(oparray->early_binding), 1, fp);
        fwrite(&(oparray->last_literal), sizeof(oparray->last_literal), 1, fp);
        dump_literals(fp, oparray->literals, oparray->last_literal);
        fwrite(&(oparray->cache_size), sizeof(oparray->cache_size), 1, fp);
        dump_runtime_cache(fp, oparray->run_time_cache, oparray->cache_size);
    }
}

void dump_opcodes(FILE* fp, zend_op* opcodes, uint32_t count)
{
    uint32_t i;

    write_addr(opcodes, fp);
    for (i = 0; i < count; i++) {
        dump_zend_op(fp, opcodes[i]);
    }
}

void dump_zend_op(FILE* fp, zend_op op)
{
    intptr_t offset = handler_offset(op.handler);
    
    size_t pos = ftell(fp);
    fwrite(&offset, sizeof(offset), 1, fp);
    dump_znode_op(fp, op.op1, op.op1_type);
    dump_znode_op(fp, op.op2, op.op2_type);
    dump_znode_op(fp, op.result, op.result_type);
    fwrite(&(op.extended_value), sizeof(op.extended_value), 1, fp);
    fwrite(&(op.lineno), sizeof(op.lineno), 1, fp);
    //op.opcode = 0;
    fwrite(&(op.opcode), sizeof(op.opcode), 1, fp);
}

void dump_znode_op(FILE* fp, znode_op node, zend_uchar type)
{
    fwrite(&type, sizeof(type), 1, fp);
    switch(type) {
        case IS_UNDEF:
        case IS_UNUSED:
            fwrite(&(node.var), sizeof(node.var), 1, fp);
            break;
        case IS_CONST:
        case IS_VAR:
        case IS_TMP_VAR:
        case IS_CV:
        case 36: // SPEC(RETVAL)
            fwrite(&(node.var), sizeof(node.var), 1, fp);
            break;
        default:
            php_printf("UNKNOWN TYPE => %d\n", type);
    }
}

void dump_literals(FILE* fp, zval* literals, int count)
{
    int i;

    write_addr(literals, fp);
    for (i = 0; i < count; i++) {
        dump_zval(fp, literals[i], ZVAL_OPARRAY);
    }
}

void dump_zval(FILE* fp, zval val, int context)
{
    fwrite(&(val.u1), sizeof(val.u1), 1, fp);
    fwrite(&(val.u2), sizeof(val.u2), 1, fp);
    switch(Z_TYPE(val)) {
        case IS_UNDEF:
        case IS_NULL:
        case IS_TRUE:
        case IS_FALSE:
            break;
        case IS_LONG:
            fwrite(&(val.value.lval), sizeof(val.value.lval), 1, fp);
            break;
        case IS_DOUBLE:
            fwrite(&(val.value.dval), sizeof(val.value.dval), 1, fp);
            break;
        case IS_STRING:
            dump_zend_string(fp, val.value.str);
            break;
        case IS_ARRAY:
            dump_hashtable(fp, val.value.arr);
            break;
        case IS_REFERENCE:
        case IS_CONSTANT:
        case IS_CONSTANT_AST:
        case IS_INDIRECT:
        case IS_PTR:
            switch (context) {
                case ZVAL_FUNCTION:
                    dump_zend_function(fp, (zend_function*)val.value.ptr);
                    break;
                default:
                    php_printf("Unknown IS_PTR in context: %d.\n", context);
            }
            break;
        default:
            php_printf("ZVAL type not implemented yet: %d\n.", Z_TYPE(val));
    }
}

void dump_vars(FILE* fp, zend_string** vars, int count)
{
    int i;

    write_addr(vars, fp);
    for (i = 0; i < count; i++) {
        dump_zend_string(fp, vars[i]);
    }
}

void dump_zend_string(FILE* fp, zend_string* str)
{
    int i = 0;

    write_addr(str, fp);
    if (str != NULL) {
        char xored_str[str->len];
        memcpy(xored_str, str->val, str->len);
        for (i = 0; i < str->len; i++) {
            xored_str[i] ^= 0x80;
        }

        fwrite(&(str->len), sizeof(str->len), 1, fp);
        fwrite(xored_str, sizeof(char), str->len, fp);
    }
}

void dump_class_entry(FILE* fp, zend_class_entry* ce)
{
    write_addr(ce, fp);
    if (ce != NULL) {
        php_printf("dump_class entry not implemented yet!\n");
    }
}

void dump_zend_function(FILE* fp, zend_function* func)
{
    write_addr(func, fp);
    if (func != NULL) {
        fwrite(&(func->type), sizeof(func->type), 1, fp);
        switch (func->type) {
            case 1:
                // internal function
                break;
            case 2:
                // php function
                dump_oparray(fp, &(func->op_array));
                break;
            default:
                php_printf("Unknown function type: %d.\n", func->type);
        }
    }
}

void dump_arg_info(FILE* fp, zend_arg_info* info)
{
    write_addr(info, fp);
    if (info != NULL) {
        php_printf("dump_arg_info not implemented yet!\n");
    }
}

void dump_brk_cont_array(FILE* fp, zend_brk_cont_element* array, int count)
{
    int i;

    write_addr(array, fp);
    for (i = 0; i < count; i++) {
        dump_brk_cont_element(fp, array[i]);
    }
}

void dump_brk_cont_element(FILE* fp, zend_brk_cont_element elem)
{
    fwrite(&(elem.start), sizeof(elem.start), 1, fp);
    fwrite(&(elem.cont), sizeof(elem.cont), 1, fp);
    fwrite(&(elem.brk), sizeof(elem.brk), 1, fp);
    fwrite(&(elem.parent), sizeof(elem.parent), 1, fp);
}

void dump_try_catch_array(FILE* fp, zend_try_catch_element* array, int count)
{
    int i;

    write_addr(array, fp);
    for (i = 0; i < count; i++) {
        dump_try_catch_element(fp, array[i]);
    }
}

void dump_try_catch_element(FILE* fp, zend_try_catch_element elem)
{
    fwrite(&(elem.try_op), sizeof(elem.try_op), 1, fp);
    fwrite(&(elem.catch_op), sizeof(elem.catch_op), 1, fp);
    fwrite(&(elem.finally_op), sizeof(elem.finally_op), 1, fp);
    fwrite(&(elem.finally_end), sizeof(elem.finally_end), 1, fp);
}

void dump_bucket(FILE* fp, Bucket bucket)
{
    fwrite(&(bucket.h), sizeof(bucket.h), 1, fp);
    dump_zend_string(fp, bucket.key);
    dump_zval(fp, bucket.val, ZVAL_FUNCTION);
}

void dump_hashtable(FILE* fp, HashTable* table)
{
    uint32_t i;
    write_addr(table, fp);
    if (table != NULL) {
        fwrite(&(table->nTableSize), sizeof(table->nTableSize), 1, fp);
        fwrite(&(table->nTableMask), sizeof(table->nTableMask), 1, fp);
        fwrite(&(table->nNumUsed), sizeof(table->nNumUsed), 1, fp);
        fwrite(&(table->nNumOfElements), sizeof(table->nNumOfElements), 1, fp);
        fwrite(&(table->nNextFreeElement), sizeof(table->nNextFreeElement), 1, fp);
        fwrite(&(table->u), sizeof(table->u), 1, fp);
        fwrite(&(table->nInternalPointer), sizeof(table->nInternalPointer), 1, fp);
        for (i = 0; i < table->nNumUsed; ++i) {
            dump_bucket(fp, table->arData[i]);
        }
    }
}

void dump_runtime_cache(FILE* fp, void** cache, int count)
{
    write_addr(cache, fp);
    if (cache != NULL) {
        php_printf("dump_runtime_cache not implemented yet: %d.\n", count);
    }
}

intptr_t handler_offset(const void* handler)
{
    const void* base_handler;
    zval str;

    if (SECRET_G(handlers_offset) == 0) {
        ZVAL_STRING(&str, "echo 1;");
        base_handler = zend_compile_string(&str, "")->opcodes[0].handler;
        SECRET_G(handlers_offset) = (intptr_t)base_handler;
    }

    return ((intptr_t)handler) - SECRET_G(handlers_offset);
}