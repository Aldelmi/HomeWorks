import hashlib
import time
import os
import sys

def calculate_file_hash(filepath):
    sha256_hash = hashlib.sha256()
    
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(4096), b""):
            sha256_hash.update(chunk)
            
    return sha256_hash.hexdigest()

def monitor_file(filepath, interval=3):
    if not os.path.exists(filepath):
        print(f"[!] Error: File '{filepath}' does not exist.")
        sys.exit(1)

    print(f"[*] Starting File Integrity Monitor on: {filepath}")
    print(f"[*] Checking every {interval} seconds. Press Ctrl+C to stop.\n")

    baseline_hash = calculate_file_hash(filepath)
    print(f"[+] Baseline Hash: {baseline_hash}")
    print("-" * 70)

    try:
        while True:
            time.sleep(interval)
            
            current_hash = calculate_file_hash(filepath)
            
            if current_hash != baseline_hash:
                print(f"\n[ CRITICAL ALERT] File has been MODIFIED!")
                print(f"    File    : {filepath}")
                print(f"    Old Hash: {baseline_hash}")
                print(f"    New Hash: {current_hash}")
                print(f"    Time    : {time.strftime('%Y-%m-%d %H:%M:%S')}")
                print("-" * 70)
                
                baseline_hash = current_hash
            else:
                print(f"[*] Checked at {time.strftime('%H:%M:%S')} - OK (No changes).")

    except KeyboardInterrupt:
        print("\n[*] Monitoring stopped by user. Goodbye!")
    except FileNotFoundError:
        print(f"\n[!] Alert: The file was DELETED!")
    except Exception as e:
        print(f"\n[!] Unexpected error: {e}")

target_file = input("Enter the path of the file to monitor: ").strip()
    
monitor_file(target_file, interval=3)