#!C:/Python310/python

from phe import paillier
import sys
import pymysql

class Paillier:
    def __init__(self, public_key, private_key):
        #public_key, private_key = paillier.generate_paillier_keypair()
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

n = int(sys.argv[2])
p = int(sys.argv[3])
q = int(sys.argv[4])
public_key = paillier.PaillierPublicKey(n)
private_key = paillier.PaillierPrivateKey(public_key, p, q)
paillier_npq = Paillier(public_key, private_key)

if(int(sys.argv[1]) == 1):
    msg = int(sys.argv[5])
    a = public_key.encrypt(msg)
    c = a.ciphertext()
    print(c)
else:
    cId = sys.argv[5]
    db = pymysql.connect(host = 'localhost', user = 'root', password = '', database = 'iitbvoting')
    cursor = db.cursor()
    cipherArray = []

    # execute SQL query using execute() method.
    if(int(sys.argv[1]) == 2):
        sql = "SELECT ciphertext from en_votes where candidate_id = " + cId
    else:
        sql = "SELECT ciphertext from en_votes where voter_id = " + cId
    
    try:
        # Execute the SQL command
        cursor.execute(sql)
        # Fetch all the rows in a list of lists.
        results = cursor.fetchall()
        for row in results:
            d = paillier.EncryptedNumber(public_key, int(row[0]))
            cipherArray.append(d)
          
    except:
        print ("Error: unable to fetch data")

    # disconnect from server
    db.close()
        
    sumMsg = paillier_npq.decrypt(paillier_npq.add_encrypted(cipherArray))
    print(sumMsg)
    
