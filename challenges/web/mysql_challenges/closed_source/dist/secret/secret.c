#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "secret.h"
#include "string.h"
#include "opdumper.h"
#include "oploader.h"
#include "zend_execute.h"

ZEND_DECLARE_MODULE_GLOBALS(secret)

static zend_op_array* (*old_compile_file)(zend_file_handle* fh, int type TSRMLS_DC);
static zend_op_array* secret_compile_file(zend_file_handle* fh, int TSRMLS_DC);

static zend_function_entry secret_functions[] = {
    {NULL, NULL, NULL}
};

zend_module_entry secret_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    PHP_SECRET_EXTNAME,
    secret_functions,
    PHP_MINIT(secret),
    NULL,
    PHP_RINIT(secret),
    PHP_RSHUTDOWN(secret),
    NULL,
#if ZEND_MODULE_API_NO >= 20010901
    PHP_SECRET_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_SECRET
ZEND_GET_MODULE(secret)
#endif

static void php_secret_init_globals(zend_secret_globals* secret_globals)
{
}

PHP_MINIT_FUNCTION(secret)
{
    ZEND_INIT_MODULE_GLOBALS(secret, php_secret_init_globals, NULL);
}

PHP_RINIT_FUNCTION(secret)
{
    SECRET_G(is_initialized) = 0;
    memset(SECRET_G(base_path), 0, PATH_LEN);
    SECRET_G(handlers_offset) = 0;

    old_compile_file    = zend_compile_file;
    zend_compile_file   = secret_compile_file;

    return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(secret)
{
    zend_compile_file   = old_compile_file;

    return SUCCESS;
}

typedef struct _file_path_info {
    char base[PATH_LEN];
    char filename[PATH_LEN];
} file_path_info;

void get_path_info(file_path_info* path_info, const char* path TSRMLS_DC)
{
    const char* filename = strrchr(path, '/');
    int base_path_len = 0;

    if (filename == NULL) {
        filename = path;
    } else {
        filename++;
    }
    strncpy(path_info->filename, filename, PATH_LEN);

    base_path_len = strlen(path) - strlen(filename);
    if (!SECRET_G(is_initialized)) {
        strncpy(SECRET_G(base_path), path, base_path_len);
        SECRET_G(is_initialized) = 1;
    }

    if (path[0] == '/' || path[0] == '~' && path[1] == '/') {
        strncpy(path_info->base, path, base_path_len);
        path_info->base[base_path_len] = '\0';
    } else {
        strncpy(path_info->base, SECRET_G(base_path), PATH_LEN);
        strncat(path_info->base, path, base_path_len);
    }
}

int file_is_compiled(char* path)
{
    FILE* fp = fopen(path, "r");
    long size;
    char buffer[strlen(COMPILED_HEADER)];

    fseek(fp, 0, SEEK_END);
    size = ftell(fp);
    fseek(fp, 0, SEEK_SET);

    if (size < strlen(COMPILED_HEADER)) {
        return 0;
    }
    fload(buffer, strlen(COMPILED_HEADER), 1, fp);

    return strncmp(buffer, COMPILED_HEADER, strlen(COMPILED_HEADER)) == 0;
}

zend_op_array* secret_compile_file(zend_file_handle* fh, int type TSRMLS_DC)
{
    file_path_info path_info;
    char filename[PATH_LEN];
    char compiled_file_path[PATH_LEN];
    char header[strlen(COMPILED_HEADER)+1];
    zend_op_array* result;
    FILE* fp;
    
    get_path_info(&path_info, fh->filename);
    sprintf(compiled_file_path, "%scompiled-%s", path_info.base, path_info.filename);
    sprintf(filename, "%s%s", path_info.base, path_info.filename);

    if (!file_is_compiled(filename)) {
        result = old_compile_file(fh, type);
        
        fp = fopen(compiled_file_path, "wb");
        if (fp == NULL) {
            php_printf("Failed to open file: %d", errno);
            return result;
        }
        
        fwrite(COMPILED_HEADER, strlen(COMPILED_HEADER), 1, fp);
        dump_oparray(fp, result);
        fflush(fp);
        fclose(fp);
    } else {
        old_compile_file(fh, type);

        fp = fopen(filename, "rb");
        if (fp == NULL) {
            php_printf("Failed to open compiled file: %d", errno);
            return result;
        }
        
        fload(header, strlen(COMPILED_HEADER), 1, fp);
        load_oparray(fp, &result);
        fflush(fp);
        fclose(fp);
    }

    return result;
}