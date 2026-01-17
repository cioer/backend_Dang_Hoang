<?php
class UserRepository {
    private $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function getByUsernameRoleIn(string $username, array $roles) {
        $in = implode("','", array_map(fn($r)=>$r, $roles));
        $sql = "SELECT id,username,password,full_name,role,is_locked,password_must_change,phone_verified,email,avatar,last_login FROM users WHERE username=:u AND role IN ('".$in."') LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":u"=>$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function getStudentByIdentifier(string $identifier) {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $stmt = $this->db->prepare("SELECT id,username,password,full_name,role,is_locked,password_must_change FROM users WHERE email=:e AND role IN ('student', 'red_star') LIMIT 1");
            $stmt->execute([":e"=>$identifier]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } else {
            $stmt = $this->db->prepare("SELECT id,username,password,full_name,role,is_locked,password_must_change FROM users WHERE username=:u AND role IN ('student', 'red_star') LIMIT 1");
            $stmt->execute([":u"=>$identifier]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }
    public function getParentByStudentId(int $studentId) {
        $stmt = $this->db->prepare("SELECT u.id,u.username,u.password,u.full_name,u.role,u.phone_verified FROM parent_student_links p JOIN users u ON p.parent_id=u.id WHERE p.student_id=:sid AND u.role='parent' LIMIT 1");
        $stmt->execute([":sid"=>$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function isLocked(array $user): bool { return intval($user['is_locked'] ?? 0) === 1; }
    public function verifyPassword(string $hash, string $password): bool {
        if (preg_match('/^\$(2y|2a|2b|argon2id|argon2i)\$/', $hash)) {
            return password_verify($password, $hash);
        }
        return hash_equals($hash, $password);
    }
    public function hashPassword(string $password): string { return password_hash($password, PASSWORD_BCRYPT); }
    public function updateLastLogin(int $id): void { $this->db->prepare("UPDATE users SET last_login=NOW() WHERE id=:id")->execute([":id"=>$id]); }
    public function updatePassword(int $id, string $password): bool {
        $h = $this->hashPassword($password);
        $stmt = $this->db->prepare("UPDATE users SET password=:p WHERE id=:id");
        return $stmt->execute([":p"=>$h, ":id"=>$id]);
    }
    public function existsUsername(string $username): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username=:u LIMIT 1");
        $stmt->execute([":u"=>$username]);
        return $stmt->rowCount() > 0;
    }
    public function createUser(string $username, string $password, string $full_name, string $role, ?string $email, ?string $phone): bool {
        $stmt = $this->db->prepare("INSERT INTO users SET username=:u, password=:p, full_name=:f, role=:r, email=:e, phone=:ph, created_at=NOW()");
        return $stmt->execute([":u"=>$username, ":p"=>$this->hashPassword($password), ":f"=>$full_name, ":r"=>$role, ":e"=>$email ?? "", ":ph"=>$phone ?? ""]);
    }
    public function createUserReturnId(string $username, string $password, string $full_name, string $role, ?string $email, ?string $phone): ?int {
        $stmt = $this->db->prepare("INSERT INTO users SET username=:u, password=:p, full_name=:f, role=:r, email=:e, phone=:ph, created_at=NOW()");
        if ($stmt->execute([":u"=>$username, ":p"=>$this->hashPassword($password), ":f"=>$full_name, ":r"=>$role, ":e"=>$email ?? "", ":ph"=>$phone ?? ""])) {
            return (int)$this->db->lastInsertId();
        }
        return null;
    }
    public function updateUser(int $id, array $fields): bool {
        $sets = [];
        $params = [":id"=>$id];
        foreach (["full_name","email","phone","role"] as $k) {
            if (array_key_exists($k, $fields) && $fields[$k] !== null && $fields[$k] !== "") {
                $sets[] = "$k=:$k"; 
                $params[":$k"] = $fields[$k];
            }
        }
        if (array_key_exists("password", $fields) && $fields["password"] !== "") {
            $sets[] = "password=:password";
            $params[":password"] = $this->hashPassword($fields["password"]);
        }
        if (!$sets) return true;
        $sql = "UPDATE users SET ".implode(",", $sets)." WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
