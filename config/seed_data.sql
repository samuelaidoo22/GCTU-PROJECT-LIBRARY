-- Seed Data for GCTU Online Project Library System

USE gctu_library;

-- 1. Create Users (Password is 'password123' hashed with bcrypt)
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (username, email, password_hash, role, department_id, status) VALUES 
('admin', 'admin@gctu.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'active'),
('kwame.asante', 'k.asante@student.gctu.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1, 'active'),
('ama.renshaw', 'a.renshaw@student.gctu.edu.gh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 2, 'active');

-- 2. Create Sample Projects
INSERT INTO projects (title, abstract, keywords, category_id, department_id, file_path, upload_date, uploader_id, approval_status, view_count) VALUES 
('Secure Cloud Storage for SMEs in Ghana', 'This study explores the implementation of robust encryption algorithms for cloud-based storage solutions tailored for small and medium enterprises in the Ghanaian business landscape. It addresses concerns regarding data sovereignty and privacy.', 'Cloud, Security, SMEs, Ghana', 3, 1, 'uploads/sample1.pdf', '2024-03-15', 2, 'approved', 145),
('AI-Powered Chatbot for Student Services', 'A project focusing on the development of a natural language processing system to handle common student inquiries at GCTU. The system uses a trained model to provide instant responses to registration and timetable queries.', 'AI, Chatbot, NLP, Education', 1, 2, 'uploads/sample2.pdf', '2024-04-10', 3, 'approved', 320),
('Blockchain-Based Voting System for SRC Elections', 'Implementation of a decentralized ledger technology to ensure transparency and security in student representative council elections. This project demonstrates how blockchain can eliminate electoral fraud and improve student trust.', 'Blockchain, Voting, SRC, Security', 3, 1, 'uploads/sample3.pdf', '2023-11-20', 2, 'approved', 87),
('IoT Smart Campus Energy Management', 'Design and implementation of a network of sensors and actuators to monitor and optimize power consumption across the GCTU main campus. The system uses real-time data to control lighting and air conditioning systems.', 'IoT, Energy, Smart Campus, Sustainability', 5, 4, 'uploads/sample4.pdf', '2024-01-05', 2, 'approved', 58);

-- 3. Create Authors
INSERT INTO authors (project_id, author_name, student_id) VALUES 
(1, 'Kwame Asante', 'GCTU/101/2020'),
(2, 'Ama Renshaw', 'GCTU/205/2020'),
(3, 'John Doe', 'GCTU/444/2021'),
(4, 'Sarah Johnson', 'GCTU/112/2020');
