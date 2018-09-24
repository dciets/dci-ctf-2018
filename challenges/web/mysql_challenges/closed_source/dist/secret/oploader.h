#ifndef OPLOADER_H_INCLUDED
#define OPLOADER_H_INCLUDED

#include "php.h"

void load_oparray(FILE* fp, zend_op_array** oparray);
void load_opcodes(FILE* fp, zend_op** opcodes, uint32_t count);
void load_zend_op(FILE* fp, zend_op* op);
void load_znode_op(FILE* fp, znode_op* node, zend_uchar* type);
void load_literals(FILE* fp, zval** literals, int count);
void load_zval(FILE* fp, zval* val);
void load_vars(FILE* fp, zend_string*** vars, int count);
void load_zend_string(FILE* fp, zend_string** str);
void load_class_entry(FILE* fp, zend_class_entry** ce);
void load_zend_function(FILE* fp, zend_function** func);
void load_arg_info(FILE* fp, zend_arg_info** info);
void load_brk_cont_array(FILE* fp, zend_brk_cont_element** array, int count);
void load_brk_cont_element(FILE* fp, zend_brk_cont_element* elem);
void load_try_catch_array(FILE* fp, zend_try_catch_element** array, int count);
void load_try_catch_element(FILE* fp, zend_try_catch_element* elem);
void load_hashtable(FILE* fp, HashTable** table);
void load_runtime_cache(FILE* fp, void*** cache, int count);
void* get_handler_by_offset(intptr_t offset TSRMLS_DC);
void fload(void* dest, size_t size, size_t n, FILE* fp);
void debug(const char* fmt, ...);

#endif