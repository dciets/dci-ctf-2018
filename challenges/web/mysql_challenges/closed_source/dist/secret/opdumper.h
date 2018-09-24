#ifndef OPDUMPER_H_INCLUDED
#define OPDUMPER_H_INCLUDED

#include "php.h"

void dump_oparray(FILE* fp, zend_op_array* oparray);
void dump_opcodes(FILE* fp, zend_op* opcodes, uint32_t count);
void dump_zend_op(FILE* fp, zend_op op);
void dump_znode_op(FILE* fp, znode_op node, zend_uchar type);
void dump_literals(FILE* fp, zval* literals, int count);
void dump_zval(FILE* fp, zval val, int context);
void dump_vars(FILE* fp, zend_string** vars, int count);
void dump_zend_string(FILE* fp, zend_string* str);
void dump_class_entry(FILE* fp, zend_class_entry* ce);
void dump_zend_function(FILE* fp, zend_function* func);
void dump_arg_info(FILE* fp, zend_arg_info* info);
void dump_brk_cont_array(FILE* fp, zend_brk_cont_element* array, int count);
void dump_brk_cont_element(FILE* fp, zend_brk_cont_element elem);
void dump_try_catch_array(FILE* fp, zend_try_catch_element* array, int count);
void dump_try_catch_element(FILE* fp, zend_try_catch_element elem);
void dump_hashtable(FILE* fp, HashTable* table);
void dump_runtime_cache(FILE* fp, void** cache, int count);
intptr_t handler_offset(const void* handler);

#endif