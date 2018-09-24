#ifndef PHP_SECRET_H
#define PHP_SECRET_H

#ifdef ZTS
#include "TSRM.h"
#endif

#define PATH_LEN 4096

#define ZVAL_OPARRAY  0xA
#define ZVAL_FUNCTION 0xB

ZEND_BEGIN_MODULE_GLOBALS(secret)
    char base_path[PATH_LEN];
    int is_initialized;
    intptr_t handlers_offset;
ZEND_END_MODULE_GLOBALS(secret)

#ifdef ZTS
#define SECRET_G(v) TSRMG(secret_globals_id, zend_secret_globals *, v)
#else
#define SECRET_G(v) (secret_globals.v)
#endif

#define PHP_SECRET_VERSION "1.0"
#define PHP_SECRET_EXTNAME "secret"

extern zend_module_entry secret_module_entry;
#define phpext_secret_ptr &secret_module_entry

PHP_MINIT_FUNCTION(secret);
PHP_RINIT_FUNCTION(secret);
PHP_RSHUTDOWN_FUNCTION(secret);

#define COMPILED_HEADER "<?php echo 'You are missing something.'; exit(); ?>"

#endif
