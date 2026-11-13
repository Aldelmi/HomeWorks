import string
import secrets
import hashlib

def generate_password(length=16):
    chars = string.ascii_letters + string.digits + string.punctuation
    
    if length < 4:
        print("[!] Warning: Password length is too short. Using minimum length of 8.")
        length = 8

    password = ''.join(secrets.choice(chars) for _ in range(length))
    return password

def calculate_sha256(text):

    encoded_text = text.encode('utf-8')
    
    sha256_hash = hashlib.sha256(encoded_text)
    
    return sha256_hash.hexdigest()

print("=" * 50)
print("  Password Generator & SHA-256 Hasher Tool  ")
print("=" * 50)

while True:
    print("\nChoose an option:")
    print("1. Generate a strong password and hash it")
    print("2. Hash an existing text")
    print("3. Exit")
        
    choice = input("\nEnter your choice (1/2/3): ").strip()

    if choice == '1':
        try:              
            pwd_length = int(input("Enter password length (default 16): ") or 16)
        except ValueError:
            print("[!] Invalid number. Using default length (16).")
            pwd_length = 16

        generated_pwd = generate_password(pwd_length)
            
        pwd_hash = calculate_sha256(generated_pwd)

        print("\n" + "-" * 40)
        print(f"[+] Generated Password : {generated_pwd}")
        print(f"[+] SHA-256 Hash       : {pwd_hash}")
        print(f"[+] Hash Length        : {len(pwd_hash)} characters")
        print("-" * 40)

    elif choice == '2':
        text_to_hash = input("Enter the text to hash: ")
        text_hash = calculate_sha256(text_to_hash)
            
        print("\n" + "-" * 40)
        print(f"[+] Input Text : {text_to_hash}")
        print(f"[+] SHA-256    : {text_hash}")
        print("-" * 40)

    elif choice == '3':
        print("[*] Exiting. Stay safe!")
        break
    else:
        print("[!] Invalid choice. Please enter 1, 2, or 3.")

