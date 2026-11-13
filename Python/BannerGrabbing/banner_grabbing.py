import socket
def grab_banner(target_ip, target_port):
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        
        sock.settimeout(1.5)
        
        sock.connect(target_ip ,target_port)
        
        if target_port in [80,8080,8000,443]:
            http_request =f"Head /HTTP / 1.1 \r \n Host : {target_ip}\n"
            sock.send(http_request.encode())
            
        banner = sock.recv(1024)
            
        decoded_banner = banner.decode('utf-8',errors='ignore').strip()
        
        if decoded_banner:
            first_line =decoded_banner.splitlines()[0]
            print(f"[+] port {target_port} : {first_line}")
        
        else:
            
            print(f"[-] port {target_port} : connected but no banner returned ")
        
        sock.close()
    except sock.timeout:
        print(f" [!] port {target_port} :timeout (No response ). ")
    
    except Exception as e:
        print(f" [!] port {target_port} : Error ({e})")

target_ip = input(" Enter The Target IP (e.g   127.0.0.1): ")
ports_input = input(" Enter ports  (e.g 21,22,80...) you should write ',' between them :")
ports = [int (p.strip()) for p in ports_input.split(',')]
print (f" [+] Starting Banner Grabbing on {target_ip} ... \n")

for port in ports:
    grab_banner(target_ip , port)

print (" \nScanning Done .")