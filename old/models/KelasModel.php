    <?php
    // File: models/KelasModel.php
    require_once dirname(__FILE__) . '/../services/database.php';

    class KelasModel
    {
        private $conn;

        public function __construct()
        {
            global $conn;
            $this->conn = $conn;
        }

        public function getAllKelas()
        {
            $query = "SELECT * FROM kelas";
            $result = mysqli_query($this->conn, $query);
            $kelasList = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $kelasList[] = $row;
            }
            return $kelasList;
        }

        public function getKelasById($kelasId)
        {
            $query = "SELECT * FROM kelas WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $kelasId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }

        public function insertKelas($data)
        {
            $query = "INSERT INTO kelas 
            (mentor_id, name_mentor, name, image, description, category, kurikulum, price, quota, quota_left, schedule, sesion_1,sesion_2,sesion_3, end_date, link_wa, link_youtube, status, what_will_learn_1, what_will_learn_2, what_will_learn_3, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Prepare the query
            $stmt = mysqli_prepare($this->conn, $query);

            // If mentor_id is empty, set it to NULL
            $mentor_id = isset($data['mentor_id']) && !empty($data['mentor_id']) ? intval($data['mentor_id']) : NULL;

            // Bind parameters individually
            mysqli_stmt_bind_param(
                $stmt,
                "isssssssiiissssssssssss",  // Updated type definition
                $mentor_id,  // This will be NULL if not provided
                $data['name_mentor'],
                $data['name'],
                $data['image'],
                $data['description'],
                $data['category'],
                $data['kurikulum'],
                $data['price'],
                $data['quota'],
                $data['quota_left'],
                $data['schedule'],
                $data['sesion_1'],
                $data['sesion_2'],
                $data['sesion_3'],
                $data['end_date'],
                $data['link_wa'],
                $data['link_youtube'],
                $data['status'],
                $data['what_will_learn_1'],
                $data['what_will_learn_2'],
                $data['what_will_learn_3'],
                $data['created_at'],
                $data['updated_at']
            );

            // Execute the query
            $result = mysqli_stmt_execute($stmt);

            // Jika eksekusi berhasil, kembalikan ID terakhir yang dimasukkan
            if ($result) {
                return mysqli_insert_id($this->conn);
            }

            return false;
        }

        public function updateKelas($kelasId, $data)
        {
            $query = "UPDATE kelas 
                    SET mentor_id = ?, name_mentor = ?, name = ?, image = ?, description = ?, category = ?, kurikulum = ?, price = ?, quota = ?, quota_left = ?, schedule = ?, sesion_1 = ?, sesion_2 = ?, sesion_3 = ?, end_date = ?, link_wa = ?, link_youtube = ?,status = ?,  what_will_learn_1 = ?, what_will_learn_2 = ?, what_will_learn_3 = ?, updated_at = ?
                    WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);

            // Determine mentor_id, use NULL if empty
            $mentor_id = isset($data['mentor_id']) && !empty($data['mentor_id']) ? intval($data['mentor_id']) : NULL;

            // Determine image path
            $image = $data['image'] ?? NULL;

            // Current timestamp for updated_at
            $updated_at = date('Y-m-d H:i:s');

            mysqli_stmt_bind_param(
                $stmt,
                "isssssssiissssssssssssi",  // Updated type definition
                $mentor_id,          // mentor_id
                $data['name_mentor'],
                $data['name'],
                $image,              // image 
                $data['description'],
                $data['category'],
                $data['kurikulum'],
                $data['price'],
                $data['quota'],
                $data['quota_left'],
                $data['schedule'],
                $data['sesion_1'],
                $data['sesion_2'],
                $data['sesion_3'],
                $data['end_date'],
                $data['link_wa'],
                $data['link_youtube'],
                $data['status'],
                $data['what_will_learn_1'],
                $data['what_will_learn_2'],
                $data['what_will_learn_3'],
                $updated_at,         // updated_at
                $kelasId             // id for WHERE clause
            );

            return mysqli_stmt_execute($stmt);
        }

        public function deleteKelas($kelasId)
        {
            $query = "DELETE FROM kelas WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $kelasId);
            return mysqli_stmt_execute($stmt);
        }

        public function attachBooksToKelas($kelasId, $bookIds)
        {

            $currentTimestamp = date('Y-m-d H:i:s');

            // Validate that the kelas exists first
            $kelasExists = $this->getKelasById($kelasId);
            if (!$kelasExists) {
                throw new Exception("Kelas with ID $kelasId does not exist.");
            }

            // Detach existing books
            $this->detachBooksFromKelas($kelasId);

            // Prepare a query to check if book exists
            $checkBookQuery = "SELECT id FROM books WHERE id = ?";
            $checkBookStmt = mysqli_prepare($this->conn, $checkBookQuery);

            // Begin a transaction
            mysqli_begin_transaction($this->conn);

            try {
                foreach ($bookIds as $bookId) {
                    // First, verify the book exists
                    mysqli_stmt_bind_param($checkBookStmt, "i", $bookId);
                    mysqli_stmt_execute($checkBookStmt);
                    $result = mysqli_stmt_get_result($checkBookStmt);

                    if (mysqli_num_rows($result) === 0) {
                        throw new Exception("Book with ID $bookId does not exist.");
                    }

                    // If book exists, insert the relationship
                    $insertQuery = "INSERT INTO book_kelas (book_id, kelas_id, created_at, updated_at) VALUES (?, ?, ?, ?)";
                    $insertStmt = mysqli_prepare($this->conn, $insertQuery);
                    mysqli_stmt_bind_param($insertStmt, "iiss", $bookId, $kelasId, $currentTimestamp, $currentTimestamp);
                    mysqli_stmt_execute($insertStmt);
                }

                // Commit the transaction
                mysqli_commit($this->conn);
                return true;
            } catch (Exception $e) {
                // Rollback the transaction on error
                mysqli_rollback($this->conn);
                // You might want to log the error or handle it appropriately
                throw $e;
            }
        }

        public function detachBooksFromKelas($kelasId)
        {
            $query = "DELETE FROM book_kelas WHERE kelas_id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $kelasId);
            mysqli_stmt_execute($stmt);
        }

        public function getKelasByCategory($category)
        {
            // Query untuk mengambil data berdasarkan kategori
            $query = "SELECT * FROM kelas WHERE LOWER(category) = LOWER(?)";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $category);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $kelasList = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $kelasList[] = $row;
            }
            return $kelasList;
        }

        public function getMentorByKelasId($kelasId)
        {
            // Query SQL untuk mengambil data mentor berdasarkan mentor_id dari tabel kelas
            $query = "
                SELECT 
                    mentors.id,
                    mentors.name,
                    mentors.email,
                    mentors.profile_picture,
                    mentors.phone_number
                FROM mentors
                INNER JOIN kelas ON kelas.mentor_id = mentors.id
                WHERE kelas.id = ?
            ";

            // Siapkan statement
            $stmt = mysqli_prepare($this->conn, $query);

            if (!$stmt) {
                error_log("Failed to prepare statement: " . mysqli_error($this->conn));
                return null;
            }

            // Bind parameter kelasId
            mysqli_stmt_bind_param($stmt, "i", $kelasId);

            // Eksekusi statement
            mysqli_stmt_execute($stmt);

            // Ambil hasil query
            $result = mysqli_stmt_get_result($stmt);

            if (!$result) {
                error_log("Query failed: " . mysqli_error($this->conn));
                return null;
            }

            // Kembalikan hasil sebagai array asosiatif (atau null jika tidak ditemukan)
            $mentor = mysqli_fetch_assoc($result);

            if (!$mentor) {
                error_log("No mentor found for class ID: " . $kelasId);
            }

            return $mentor;
        }
    }
