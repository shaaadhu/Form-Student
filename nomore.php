<?php include('config.php');?>

<?php

try{
    $sqlt_table = "CREATE TABLE IF NOT EXISTS student(
        id INT(10) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50) UNIQUE NOT NULL,
        address VARCHAR(50) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        gender VARCHAR(20),
        level VARCHAR(100),
        file MEDIUMBLOB NULL,
        captcha INT(1) DEFAULT 0
    )";
    $conn->exec($sqlt_table);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) ) {
        
        $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"] );
        $address = htmlspecialchars($_POST["address"] );
        $phone = htmlspecialchars($_POST["phone"] );
        $gender = htmlspecialchars($_POST["gender"] );
        $level = htmlspecialchars($_POST["level"] );
        $captcha_verified = ( $_POST["captcha"] == 'on') ? 1 : 0; 
        
        $image_data = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $image_data = file_get_contents($_FILES['file']['tmp_name']);
        }

    
        $sql_insert = "INSERT INTO student (name, email, address, phone, gender, level, file, captcha) 
                      VALUES (:name, :email, :address, :phone, :gender, :level, :file_data, :captcha_v)";   
        $stmt = $conn->prepare($sql_insert);
         
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':level', $level);
        $stmt->bindParam(':captcha_v', $captcha_verified, PDO::PARAM_INT);
        $stmt->bindParam(':file_data', $image_data, PDO::PARAM_LOB); 
        
        $stmt->execute();
        $message = "<p class='alert alert-success mt-3'>  Data for $name added .</p>";
    }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) { 

    $id = $_POST['id'];
    $name = htmlspecialchars($_POST["name"]);
        $email = htmlspecialchars($_POST["email"] );
        $address = htmlspecialchars($_POST["address"] );
        $phone = htmlspecialchars($_POST["phone"] );
        $gender = htmlspecialchars($_POST["gender"] );
        $level = htmlspecialchars($_POST["level"] );
}   

}catch (PDOException $e) {
        $message .= "<p class='alert alert-danger'> Error retrieving data: " . $e->getMessage() . "</p>";
    }

$records = [];
if (isset($conn)) {
    try {
        $sql_select = "SELECT id, name, email, address, phone,gender,level,captcha FROM student ORDER BY id";
        $stmt_select = $conn->query($sql_select);
        $records = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message .= "<p class='alert alert-danger'> Error retrieving data: " . $e->getMessage() . "</p>";
    }
}  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Application Form & Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/css/intlTelInput.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="full-body d-flex flex-column align-items-center">

    <div class="main-container m-5 bg-light"> 
        <h2 class="text-center mt-3">Student Application form</h2>
        <hr class="m-2 p-2">
        
        <?php echo $message; ?>

        <form  class="form-container px-4 py-1 text-white" method="post" enctype="multipart/form-data">
            <div class="mb-3 mt-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control custom-input" id="name" name="name" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control custom-input" id="email" name="email" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control custom-input" id="address" name="address" required>
            </div>
            <div class="mb-3 mt-3 d-flex flex-column">
                <label for="phone" class="form-label">Phone Number:</label>
                <input type="tel" class="form-control custom-input" id="phone" name="phone" required>
            </div>
            <div class="mb-4">
                <label class=" d-block">Gender</label>
                <div class="d-flex">
                    <div class="form-check me-5">
                        <input class="form-check-input " type="radio" name="gender" id="male" value="male" required>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                        <label class="form-check-label " for="female">Female</label>
                    </div>
                </div>
            </div>

            <label for="studies" class="form-label">Level of studies</label>
            <div class="mb-4">
                <select class="form-select custom-input" aria-label="Default select example" name="level" required>
                    <option value="" selected>Open this select menu</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            
            <div class="mb-5">
                <label class="form-label text-white d-block">Upload your Transcript</label>
                <input type="file" id="transcript_file" name="file" style="display: none;" accept=".pdf,.doc,.docx"> 

                <button type="button" class="btn custom-upload-btn" onclick="document.getElementById('transcript_file').click()">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Click to upload file
                </button>
                <span id="file_name_display" class="d-block mt-2 text-white-50">No file selected.</span>
            </div>

            <div class="d-flex justify-content-center w-100 mb-4 ">
                <div class="d-flex justify-content-around bg-white captcha p-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="robotCheck" name="captcha">
                        <label class="form-check-label text-dark" for="robotCheck"><p>I'm not a robot</p></label>
                    </div>
                    <div class="captcha-img-placeholder">
                        <img src="img/recaptcha-icon.svg " alt="reCAPTCHA" class="img-fluid" width="40px">
                    </div>
                </div>
            </div>

            <div class="mb-5 pb-4">
                <button type="submit" class="btn custom-submit-btn float-end mt-3" name="submit">Submit</button>
            </div>
        </form>
    </div>

    <div class="record-container my-5 p-4 bg-light shadow w-75">
        <h2 class="text-center">Current Student Records</h2>
        
        <?php if (count($records) > 0): ?>
            <div class="table-responsive">
                <table class="table mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Level</th>
                            <th>Captcha</th>
                            <th>Actions</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['level']); ?></td>
                                <td><?php echo htmlspecialchars($row['captcha']); ?></td>
                                <td>
                                    <button 
                        class="btn btn-primary btn-sm edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal"
                        data-record='<?= json_encode($r) ?>'>
                        Edit
                    </button>
                                    <button 
                        class="btn btn-danger btn-sm delete-frontend-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#frontendDeleteModal"
                        data-row-id="row-<?= $r['id'] ?>">
                        Delete
                    </button>
                                </td>
                                
                                    
    
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center">Database Empty...</p>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
    <h5>Edit Record</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<form method="POST">

    <input type="hidden" id="edit-id" name="id">

    <label>Name:</label>
    <input type="text" id="edit-name" name="name" class="form-control">

    <label>Email:</label>
    <input type="email" id="edit-email" name="email" class="form-control">

    <label>Address:</label>
    <input type="text" id="edit-address" name="address" class="form-control">

    <label>Phone:</label>
    <input type="tel" id="edit-phone" name="phone" class="form-control">

    <label class="mt-3">Gender:</label><br>
    <input type="radio" name="gender" id="gen-male" value="male"> Male
    <input type="radio" name="gender" id="gen-female" value="female"> Female

    <label class="mt-3">Level:</label>
    <select id="edit-level" name="level" class="form-control">
        <option value="">Select Level</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
    </select>

    <div class="mt-3">
        <input type="checkbox" name="captcha" id="edit-captcha"> I'm not a robot
    </div>

    <button name="update" class="btn btn-dark mt-3">Update</button>
</form>

</div>
</div>
</div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/js/intlTelInput.min.js"></script>
    <script src="script.js"></script>
</body>
</html>