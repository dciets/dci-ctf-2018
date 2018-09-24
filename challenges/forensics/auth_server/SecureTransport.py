def xor(plaintext, key):
    ciphertext = b""

    for pi in range(len(plaintext)):
        ki = pi % len(key)
        ciphertext += bytes([plaintext[pi] ^ key[ki]])

    return ciphertext

class SecureTransport:
    def __init__(self, transport, key):
        self.transport = transport
        self.key = key

    def decrypt(self, data):
        if len(data) > len(self.key):
            return None

        return xor(data, self.key)

    def write(self, data):
        self.transport.write(xor(data, self.key))
