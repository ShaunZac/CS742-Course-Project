"""Implement Paillier Homomorphic Algorithm"""


def gcd(x, y):
    """Implement Euclid's algorithm for finding GCD."""
    while y > 0:
        x_val = x
        y_val = y
        x = y_val
        y = x_val % y_val
    return x


def lcm(x, y):
    """Get LCM using product and GCD."""
    return x * y // gcd(x, y)


def inverse_mod(a, b):
    """Calculate a -1 mod b. Returns -1 if it does not exist."""
    for i in range(1, b):
        if ((a % b) * (i % b)) % b == 1:
            return i
    assert gcd(a, b) == 1, "Inverse mod does not exist!"
    return -1


def l_function(x, n):
    """Implement L function."""
    return (x - 1) // n


def key_gen(p, q):
    """Key generation as per Paillier Homomorphic algorithm."""
    n = p * q
    assert gcd(n, (p - 1) * (q - 1)) == 1, "Please choose appropriate primes! Must have gcd(pq, (p - 1)(q - 1)) = 1"
    key_lambda = lcm(p - 1, q - 1)
    g = 3
    mu = inverse_mod(l_function((g ** key_lambda) % (n ** 2), n), n)
    private_key = (key_lambda, mu)
    public_key = (n, g)
    return public_key, private_key


def encrypt(m, n, g):
    """Encryption as per Paillier Homomorphic algorithm."""
    r = 7
    n_sq = n ** 2
    ciphertext = (((g ** m) % n_sq) * ((r ** n) % n_sq)) % n_sq
    return ciphertext


def decrypt(c, key_lambda, mu, n):
    """Decryption as per Paillier Homomorphic algorithm."""
    n_sq = n ** 2
    message = (l_function(((c % n_sq) ** key_lambda) % n_sq, n) * mu) % n
    return message


(n, g), (l, m) = key_gen(311, 313)
c1 = encrypt(220, n, g)
c2 = encrypt(1020, n, g)
print(decrypt(c1 * c2, l, m, n))
