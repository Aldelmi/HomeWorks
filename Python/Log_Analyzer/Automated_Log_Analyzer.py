import re

patterns = {
    "SQL Injection": re.compile(r"(UNION\s+SELECT|OR\s+1=1|DROP\s+TABLE)", re.IGNORECASE),
    "Path Traversal": re.compile(r"(\.\./|etc/passwd|boot\.ini)", re.IGNORECASE),
    "Cross-Site Scripting (XSS)": re.compile(r"(<script>|alert\()", re.IGNORECASE)
}

def analyze_log(file_path):
    attack_stats = {attack_type: 0 for attack_type in patterns}
    total_attacks = 0

    try:
        with open(file_path, 'r', encoding='utf-8') as log_file:
            print(f"[*] Starting analysis on: {file_path}\n")
            
            for line_num, line in enumerate(log_file, start=1):
                

                ip_match = re.search(r"^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})", line)
                attacker_ip = ip_match.group(1) if ip_match else "Unknown IP"
                
                for attack_type, pattern in patterns.items():
                    if pattern.search(line):
                        print(f"[ALERT] Line {line_num} | {attack_type} detected from IP: {attacker_ip}")
                        
                        attack_stats[attack_type] += 1
                        total_attacks += 1

    except FileNotFoundError:
        print(f"[!] Error: The file '{file_path}' was not found. Please check the path.")
        return
    except Exception as e:
        print(f"[!] An unexpected error occurred: {e}")
        return

    print("\n" + "="*40)
    print("        SCAN SUMMARY REPORT")
    print("="*40)
    print(f"Total Threats Detected: {total_attacks}")
    for attack_type, count in attack_stats.items():
        if count > 0:
            print(f" - {attack_type}: {count} times")
    print("="*40)



target_file = input("Enter the path to the log file (e.g., access.log): ")
analyze_log(target_file)