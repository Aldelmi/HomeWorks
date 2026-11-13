import socket
import concurrent.futures
import time

def check_port(target_ip, port):
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(1.0)        
        result = sock.connect_ex((target_ip, port))
        
        if result == 0:
            print(f"[*] Port {port} is open")
            
        sock.close()
        
    except socket.timeout:
        pass
    except socket.error:
        pass
    except Exception as e:
        pass

target = input("Enter the target IP address (e.g., 127.0.0.1): ")
    
print(f"\n[+] Starting scan on {target}...")
print("[+] Scanning ports 1 to 1024 using 50 threads...\n")
    
start_time = time.time()
    
with concurrent.futures.ThreadPoolExecutor(max_workers=50) as executor:
    for port in range(1, 1025):
        executor.submit(check_port, target, port)
            
end_time = time.time()
total_time = end_time - start_time    
print(f"\n[+] Scan completed in {total_time:.2f} seconds.")

