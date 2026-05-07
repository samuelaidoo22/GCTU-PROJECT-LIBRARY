<?php
/**
 * Mock PDO Layer — GCTU Project Library
 * Provides a complete simulated database for offline/demo use.
 * Admin login: username=admin / password=admin123
 * Student login: username=student / password=student123
 */

class MockPDO {
    public $data = [];

    public function __construct() { $this->seed(); }

    private function seed() {
        // ---- Departments ----
        $this->data['departments'] = [
            ['department_id'=>1,'department_name'=>'Information Technology','department_code'=>'IT'],
            ['department_id'=>2,'department_name'=>'Computer Science','department_code'=>'CS'],
            ['department_id'=>3,'department_name'=>'Business School','department_code'=>'BUS'],
            ['department_id'=>4,'department_name'=>'Engineering','department_code'=>'ENG'],
            ['department_id'=>5,'department_name'=>'Mathematical Sciences','department_code'=>'MATH'],
            ['department_id'=>6,'department_name'=>'Communication Studies','department_code'=>'COM'],
        ];

        // ---- Categories ----
        $this->data['categories'] = [
            ['category_id'=>1,'category_name'=>'Artificial Intelligence','description'=>'AI and ML'],
            ['category_id'=>2,'category_name'=>'Web Development','description'=>'Web Systems'],
            ['category_id'=>3,'category_name'=>'Network Security','description'=>'Cybersecurity'],
            ['category_id'=>4,'category_name'=>'Data Science','description'=>'Data Analysis'],
            ['category_id'=>5,'category_name'=>'Software Engineering','description'=>'Software Dev'],
        ];

        // ---- Projects (approved + pending for admin view) ----
        $this->data['projects'] = [
            [
                'project_id'=>1,'title'=>'Secure Cloud Storage for SMEs in Ghana',
                'abstract'=>'This study explores the implementation of robust encryption algorithms for cloud-based storage solutions tailored for small and medium enterprises in Ghana. It evaluates the performance and security of AES-256 encryption in a distributed cloud environment.',
                'keywords'=>'Cloud, Security, SMEs, AES, Ghana',
                'category_id'=>3,'department_id'=>1,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2024-03-15','view_count'=>145,'approval_status'=>'approved',
                'department_name'=>'Information Technology','category_name'=>'Network Security',
                'uploader'=>'kwame.asante','username'=>'student'
            ],
            [
                'project_id'=>2,'title'=>'AI-Powered Chatbot for Student Services',
                'abstract'=>'A project focusing on the development of a natural language processing system to handle common student inquiries at GCTU. The system uses a transformer-based model fine-tuned on university-specific datasets.',
                'keywords'=>'AI, Chatbot, NLP, Education, GCTU',
                'category_id'=>1,'department_id'=>2,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2024-04-10','view_count'=>320,'approval_status'=>'approved',
                'department_name'=>'Computer Science','category_name'=>'Artificial Intelligence',
                'uploader'=>'ama.mensah','username'=>'student'
            ],
            [
                'project_id'=>3,'title'=>'Blockchain-Based Voting System for SRC',
                'abstract'=>'Implementation of a decentralized ledger technology to ensure transparency and security in student representative council elections. The system guarantees immutability and auditability of all votes cast.',
                'keywords'=>'Blockchain, Voting, Decentralization, Security',
                'category_id'=>3,'department_id'=>1,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2023-11-20','view_count'=>87,'approval_status'=>'approved',
                'department_name'=>'Information Technology','category_name'=>'Network Security',
                'uploader'=>'kofi.boateng','username'=>'student'
            ],
            [
                'project_id'=>4,'title'=>'Real-Time Traffic Monitoring System Using IoT Sensors',
                'abstract'=>'This project presents a scalable IoT-based traffic monitoring solution for urban roads in Accra. Sensor data is aggregated in real-time and visualized on a central dashboard.',
                'keywords'=>'IoT, Traffic, Smart City, Sensors',
                'category_id'=>5,'department_id'=>4,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2024-01-08','view_count'=>62,'approval_status'=>'pending',
                'department_name'=>'Engineering','category_name'=>'Software Engineering',
                'uploader'=>'abena.osei','username'=>'student'
            ],
            [
                'project_id'=>5,'title'=>'Mobile Health App for Maternal Care in Rural Communities',
                'abstract'=>'A cross-platform mobile application designed to connect rural pregnant women with healthcare providers through telemedicine features, appointment scheduling, and health education resources.',
                'keywords'=>'mHealth, Maternal Care, Telemedicine, Rural',
                'category_id'=>2,'department_id'=>2,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2024-02-14','view_count'=>198,'approval_status'=>'pending',
                'department_name'=>'Computer Science','category_name'=>'Web Development',
                'uploader'=>'yaw.ofori','username'=>'student'
            ],
            [
                'project_id'=>6,'title'=>'Predictive Analytics for Student Academic Performance',
                'abstract'=>'Using machine learning regression models to predict student GPA based on attendance, assignment scores, and demographic data. The model achieves 87% accuracy on held-out test sets.',
                'keywords'=>'Machine Learning, Academic, Prediction, Data Science',
                'category_id'=>4,'department_id'=>5,'uploader_id'=>2,'file_path'=>'uploads/sample.pdf',
                'upload_date'=>'2023-09-05','view_count'=>231,'approval_status'=>'approved',
                'department_name'=>'Mathematical Sciences','category_name'=>'Data Science',
                'uploader'=>'student','username'=>'student'
            ],
        ];

        // ---- Users — with bcrypt hashes ----
        // admin123 / student123
        $this->data['users'] = [
            [
                'user_id'=>1,'username'=>'admin','email'=>'admin@gctu.edu.gh',
                'password_hash'=> password_hash('admin123', PASSWORD_BCRYPT),
                'role'=>'admin','department_id'=>1,'status'=>'active'
            ],
            [
                'user_id'=>2,'username'=>'student','email'=>'student@gctu.edu.gh',
                'password_hash'=> password_hash('student123', PASSWORD_BCRYPT),
                'role'=>'student','department_id'=>1,'status'=>'active'
            ],
        ];

        // ---- Authors ----
        $this->data['authors'] = [
            ['author_id'=>1,'project_id'=>1,'author_name'=>'Kwame Asante'],
            ['author_id'=>2,'project_id'=>2,'author_name'=>'Ama Mensah'],
            ['author_id'=>3,'project_id'=>2,'author_name'=>'John Tetteh'],
            ['author_id'=>4,'project_id'=>3,'author_name'=>'Kofi Boateng'],
            ['author_id'=>5,'project_id'=>4,'author_name'=>'Abena Osei'],
            ['author_id'=>6,'project_id'=>5,'author_name'=>'Yaw Ofori'],
            ['author_id'=>7,'project_id'=>6,'author_name'=>'Efua Darko'],
        ];

        // ---- Access logs ----
        $this->data['access_logs'] = [
            ['log_id'=>1,'user_id'=>2,'project_id'=>1,'username'=>'student','title'=>'Secure Cloud Storage for SMEs in Ghana','access_type'=>'download_full','access_date'=>date('Y-m-d H:i:s', strtotime('-30 minutes'))],
            ['log_id'=>2,'user_id'=>2,'project_id'=>2,'username'=>'student','title'=>'AI-Powered Chatbot for Student Services','access_type'=>'view_abstract','access_date'=>date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['log_id'=>3,'user_id'=>1,'project_id'=>3,'username'=>'admin','title'=>'Blockchain-Based Voting System for SRC','access_type'=>'view_abstract','access_date'=>date('Y-m-d H:i:s', strtotime('-2 hours'))],
            ['log_id'=>4,'user_id'=>2,'project_id'=>6,'username'=>'student','title'=>'Predictive Analytics for Student Academic Performance','access_type'=>'download_full','access_date'=>date('Y-m-d H:i:s', strtotime('-3 hours'))],
        ];
    }

    public function query($sql) {
        $stmt = new MockStmt($this->data, $sql);
        $stmt->execute([]);
        return $stmt;
    }

    public function prepare($sql) {
        return new MockStmt($this->data, $sql);
    }

    public function setAttribute($a, $b) {}
    public function beginTransaction()    {}
    public function commit()              {}
    public function rollBack()            {}
    public function lastInsertId()        { return rand(10, 999); }
}

class MockStmt {
    private $data;
    private $sql;
    private $results = null;

    public function __construct($data, $sql) {
        $this->data = $data;
        $this->sql  = $sql;
    }

    public function execute($params = []) {
        $sql = strtolower($this->sql);

        // ---- COUNT queries ----
        if (strpos($sql, 'count(*)') !== false) {
            if (strpos($sql, 'approval_status') !== false) {
                if (strpos($sql, "'approved'") !== false || strpos($sql, '"approved"') !== false || strpos($sql, "= 'approved'") !== false) {
                    $this->results = count(array_filter($this->data['projects'], fn($p) => $p['approval_status'] === 'approved'));
                } elseif (strpos($sql, "'pending'") !== false || strpos($sql, "= 'pending'") !== false) {
                    $this->results = count(array_filter($this->data['projects'], fn($p) => $p['approval_status'] === 'pending'));
                } else {
                    $this->results = count($this->data['projects']);
                }
            } elseif (strpos($sql, 'projects') !== false) {
                $this->results = count($this->data['projects']);
            } elseif (strpos($sql, "role = 'student'") !== false || strpos($sql, "role='student'") !== false) {
                $this->results = count(array_filter($this->data['users'], fn($u) => $u['role'] === 'student'));
            } elseif (strpos($sql, 'users') !== false) {
                $this->results = count($this->data['users']);
            } elseif (strpos($sql, 'departments') !== false) {
                $this->results = count($this->data['departments']);
            } elseif (strpos($sql, 'access_logs') !== false) {
                if (strpos($sql, "download_full") !== false) {
                    $this->results = count(array_filter($this->data['access_logs'], fn($l) => $l['access_type'] === 'download_full'));
                } else {
                    $this->results = count($this->data['access_logs']);
                }
            } elseif (strpos($sql, 'authors') !== false) {
                $this->results = 0;
            } else {
                $this->results = 0;
            }
            return true;
        }

        // ---- UPDATE / INSERT / DELETE ----
        if (strpos($sql, 'update') === 0 || strpos($sql, 'insert') === 0 || strpos($sql, 'delete') === 0) {
            $this->results = true;
            return true;
        }

        // ---- Users — login lookup ----
        if (strpos($sql, 'from users') !== false) {
            if (!empty($params)) {
                $username = $params[0];
                $found = null;
                foreach ($this->data['users'] as $u) {
                    if ($u['username'] === $username || (isset($u['email']) && $u['email'] === $username)) {
                        $found = $u; break;
                    }
                }
                $this->results = $found;
            } else {
                $this->results = $this->data['users'];
            }
            return true;
        }

        // ---- Projects ----
        if (strpos($sql, 'from projects') !== false) {
            $projects = $this->data['projects'];

            // Filter by approval status
            if (strpos($sql, "approval_status = 'approved'") !== false || strpos($sql, "approval_status='approved'") !== false) {
                $projects = array_values(array_filter($projects, fn($p) => $p['approval_status'] === 'approved'));
            } elseif (strpos($sql, "approval_status = 'pending'") !== false || strpos($sql, "approval_status='pending'") !== false) {
                $projects = array_values(array_filter($projects, fn($p) => $p['approval_status'] === 'pending'));
            }

            // Filter by ID
            if (!empty($params) && strpos($sql, 'project_id = ?') !== false) {
                $id = (int)$params[0];
                $match = array_values(array_filter($projects, fn($p) => $p['project_id'] == $id));
                $this->results = $match[0] ?? null;
                return true;
            }

            // Filter by department
            if (!empty($params) && strpos($sql, 'department_id = ?') !== false) {
                $id = (int)$params[0];
                $projects = array_values(array_filter($projects, fn($p) => $p['department_id'] == $id));
            }

            // LIKE search (title, keywords, abstract)
            if (!empty($params) && strpos($sql, 'like ?') !== false) {
                $term = strtolower(str_replace('%', '', $params[0]));
                $projects = array_values(array_filter($projects, function($p) use ($term) {
                    return strpos(strtolower($p['title']),    $term) !== false
                        || strpos(strtolower($p['keywords']), $term) !== false
                        || strpos(strtolower($p['abstract']), $term) !== false;
                }));
            }

            // LIMIT
            if (preg_match('/limit (\d+)/i', $this->sql, $m)) {
                $projects = array_slice($projects, 0, (int)$m[1]);
            }

            $this->results = $projects;
            return true;
        }

        // ---- Authors ----
        if (strpos($sql, 'from authors') !== false) {
            if (!empty($params)) {
                $pid = (int)$params[0];
                $this->results = array_values(array_filter($this->data['authors'], fn($a) => $a['project_id'] == $pid));
            } else {
                $this->results = $this->data['authors'];
            }
            return true;
        }

        // ---- Departments ----
        if (strpos($sql, 'from departments') !== false) {
            // dept stats for chart (returns 'cnt' to match SQL alias)
            if (strpos($sql, 'count(p.project_id)') !== false || strpos($sql, 'count(p.') !== false) {
                $result = [];
                foreach ($this->data['departments'] as $d) {
                    $c = count(array_filter($this->data['projects'], fn($p) => $p['department_id'] == $d['department_id']));
                    $result[] = array_merge($d, ['cnt' => $c, 'count' => $c]);
                }
                $this->results = $result;
            } else {
                $this->results = $this->data['departments'];
            }
            return true;
        }

        // ---- Categories ----
        if (strpos($sql, 'from categories') !== false) {
            $this->results = $this->data['categories'];
            return true;
        }

        // ---- Access Logs ----
        if (strpos($sql, 'from access_logs') !== false) {
            $this->results = $this->data['access_logs'];
            return true;
        }

        $this->results = [];
        return true;
    }

    public function fetchColumn() { return is_numeric($this->results) ? $this->results : 0; }
    public function fetch()       { return $this->results; }
    public function fetchAll()    { return is_array($this->results) ? array_values($this->results) : []; }
}
