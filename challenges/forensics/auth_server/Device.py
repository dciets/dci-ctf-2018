import random

class Device:
    def __init__(self, ip, mac=None):
        if mac is None:
            mac = [0x20, 0x37, 0x06] + [random.randint(0, 0xff) for _ in range(3)]
            mac = "{:02x}:{:02x}:{:02x}:{:02x}:{:02x}:{:02x}".format(*mac)

        self.mac = mac
        self.ip = ip
        self.next_port = 49152

    def next_client_port(self):
        port = self.next_port

        if port == 65535:
            self.next_port = 49152
        else:
            self.next_port += 1

        return port