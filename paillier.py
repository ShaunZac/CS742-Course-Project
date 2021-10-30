from phe import paillier


class Paillier:
    def __init__(self):
        public_key, private_key = paillier.generate_paillier_keypair()
        self.public_key = public_key
        self.private_key = private_key

    def encrypt(self, message):
        return self.public_key.encrypt(message)

    def decrypt(self, ciphertext):
        return self.private_key.decrypt(ciphertext)

    def add_encrypted(self, c):
        sum_ciphertext = c[0]
        for ciphertext in c[1:]:
            sum_ciphertext = sum_ciphertext + ciphertext
        return sum_ciphertext


trial = Paillier()
num1 = trial.encrypt(100)
num2 = trial.encrypt(200)

public_key_trial = paillier.PaillierPublicKey(num1.public_key.n)
num3 = paillier.EncryptedNumber(public_key_trial, num1.ciphertext)

trial.decrypt(trial.add_encrypted([num1, num2]))
