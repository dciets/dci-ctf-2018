class TCPStreamTransport:
    def __init__(self, function):
        self.function = function

    def write(self, data):
        self.function(data)