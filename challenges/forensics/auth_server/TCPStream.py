from scapy.all import TCP, Ether, IP

class TCPStream:
    def __init__(self, client, server, service, client_port=None):
        if client_port is None:
            client_port = client.next_client_port()

        self.client = client
        self.server = server
        self.service = service
        self.client_port = client_port

        # Client SEQ/ACK
        self.cs = 0
        self.ca = 0

        # Server SEQ/ACK
        self.ss = 0
        self.sa = 0

        self.packets = []

    def handshake(self):
        self.client_tcp("S")
        self.server_tcp("SA")
        self.client_tcp("A", count=False)

    def fin(self):
        self.client_tcp("FA")
        self.server_tcp("A", count=False)
        self.server_tcp("FA")
        self.client_tcp("A", count=False)

    def client_send(self, payload):
        self.client_tcp("PA", payload)
        self.server_tcp("A", count=False)

    def server_send(self, payload):
        self.server_tcp("PA", payload)
        self.client_tcp("A", count=False)

    def client_tcp(self, flags, payload=b"", count=True):
        data_length = max(len(payload), 1)

        tcp = TCP()
        tcp.flags = flags
        tcp.sport = self.client_port
        tcp.dport = self.service
        tcp.seq = self.cs
        tcp.ack = self.ca

        if count:
            self.cs += data_length
            self.sa += data_length

        packet = Ether(src=self.client.mac, dst=self.server.mac)
        packet /= IP(src=self.client.ip, dst=self.server.ip)
        packet /= tcp

        if len(payload) > 0:
            packet /= payload

        self.packets.append(packet)


    def server_tcp(self, flags, payload=b"", count=True):
        data_length = max(len(payload), 1)

        tcp = TCP()
        tcp.flags = flags
        tcp.sport = self.service
        tcp.dport = self.client_port
        tcp.seq = self.ss
        tcp.ack = self.sa

        if count:
            self.ss += data_length
            self.ca += data_length

        packet = Ether(src=self.server.mac, dst=self.client.mac)
        packet /= IP(src=self.server.ip, dst=self.client.ip)
        packet /= tcp

        if len(payload) > 0:
            packet /= payload

        self.packets.append(packet)