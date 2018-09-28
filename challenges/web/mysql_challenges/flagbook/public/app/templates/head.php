<head>
    <title><?php echo $this->data["data"]["title"]; ?></title>
    <link rel="stylesheet" type="text/css" href="api/file?url=assets/css/bootstrap.min.css&type=css">
    <link rel="stylesheet" type="text/css" href="api/file?url=assets/css/styles.css&type=css">
    <script src="/api/file?url=assets/js/jquery.min.js&type=js"></script>
    <script>
        function e(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</head>