#!/bin/python3

def frombase64(s, key):
    b64s = key
    b64p = "="
    ret = ""
    s2 = s.replace(b64p, "")
    left = 0
    for i in range(0, len(s2)):
        if left == 0:
            left = 6
        else:
            value1 = b64s.index(s2[i - 1]) & (2 ** left - 1)
            value2 = b64s.index(s2[i]) >> (left - 2)
            value = (value1 << (8 - left)) | value2
            ret += chr(value)
            left -= 2
    return ret

normal = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"
diff1 = "SPQVWTUZ[XY^_\\]BC@AFGDEJKHspqvwtuz{xy~1|}bc`afgdejkh\"# !&\'$%*+9l"
diff2 = "z0123456789+/ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxy"
diff3 = "ABCdEfGHIJKLMNoPQRSTUVWXyZabDceFghijklmnOpqrstuvwxYz0123456789+/"

ans = ""

with open("FLAG.flag", "r") as f:
    data = f.read()
i = 0
while i < len(data):
    key1 = ""
    ans1 = ""
    key2 = ""
    ans2 = ""
    key3 = ""
    ans3 = ""
    key4 = ""

    if i < len(data):
        key1 = frombase64(data[i:i+8],diff1)

    if i+8 < len(data):
        temp = frombase64(data[i+8:i+16], normal)
        for a in range(len(temp)):
            ans1 += chr(ord(temp[a]) ^ ord(key1[a]))

    if i+16 < len(data):
        temp = frombase64(data[i+16:i+20],diff3)
        for a in temp:
            key2 += chr(ord(a) ^ 0x48)

    if i+28 < len(data):
        temp = frombase64(data[i+28:i+36],diff2)
        for a in temp:
            key3 += chr(ord(a) // 2)

    if i+20 < len(data):
        temp = frombase64(data[i+20:i+28], diff1)
        for a in range(len(temp)):
            ans2 += chr(ord(temp[a]) ^ ord(key3[a]) ^ 0x89 ^ 0x36)

    if i+44 < len(data):
        key4 = frombase64(data[i+44:i+52],normal)

    if i+36 < len(data):
        temp = frombase64(data[i+36:i+44], diff3)
        for a in range(len(temp)):
            ans3 += chr(ord(temp[a]) ^ (ord(key4[a])*2) ^ 0x12 ^ 0x02)

    print(key1)
    print(ans1)
    print(key2)
    print(ans2)
    print(key3)
    print(ans3)
    print(key4)

    i += 52
