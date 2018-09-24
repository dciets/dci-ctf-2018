PHP_ARG_ENABLE(secret, whether to enable Secret,
[  --enable-secret         Enable Secret])

if test "$PHP_SECRET" = "yes"; then
  AC_DEFINE(HAVE_SECRET, 1, [Whether you have Secret])
  PHP_NEW_EXTENSION(secret, secret.c opdumper.c oploader.c, $ext_shared)
fi
