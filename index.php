<?php include('config.php'); ?>

<?php
$message = "";


try {
    $sqlt_table = "CREATE TABLE IF NOT EXISTS student(
        id INT(10) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50)  NOT NULL,
        address VARCHAR(50) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        gender VARCHAR(20),
        level VARCHAR(100),
        is_deleted TINYINT(1) DEFAULT 0
    )";
    $conn->exec($sqlt_table);


} catch (PDOException $e) {
    echo $e->getMessage();
}

catch (PDOException $e) {
    $message .= "<p class='alert alert-danger'>Error retrieving data.</p>";
}
$records = [];
try {
    $stmt = $conn->query("SELECT * FROM student WHERE is_deleted = 0 ORDER BY id");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message .= "<p class='alert alert-danger'>Error retrieving data.</p>";
}
$deleted = [];
try {
    $stmt2 = $conn->query("SELECT * FROM student WHERE is_deleted = 1 ORDER BY id");
    $deleted = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}


$conn = null;
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
<body >

    <div class="main-container  m-5 bg-light  mx-auto"> 
        <h2 class="text-center py-4">Student Application form</h2>
        <hr class="m-2 p-2">
        
        <?php echo $message; ?>

        <form id="insert-form" class="form-container px-4 py-1 text-white" method="POST">
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
                <select class="form-select custom-input" aria-label="Default select example" name="level" id="dropdown" required>
                    <option value="" selected>Open this select menu</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            
            <!-- <div class="mb-5">
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
            </div> -->

            <div class="mb-5 pb-4">
                <!-- <button type="submit" class="btn custom-submit-btn float-end mt-3" name="submit">Submit</button> -->
                <button name="submit" class="btn custom-submit-btn mt-3 float-end">Submit</button>
            </div>
        </form>
    </div>


<div class="container table-responsive">
    <h1 class="text-center ">Student Records</h1>
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Address</th>
            <th>Phone</th><th>Gender</th><th>Level</th><th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($records as $r): ?>
        <tr id="row-<?= $r['id'] ?>">
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['address']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['gender']) ?></td>
            <td><?= htmlspecialchars($r['level']) ?></td>
            

            <td>
                <button 
                    class="btn btn-primary btn-sm edit-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#editModal"
                    data-record='<?= json_encode($r) ?>'>

                    Edit
                </button>

                <button 

                class="btn btn-danger btn-sm open-delete-modal"
                data-id="<?= $r['id'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#deleteConfirmModal"
                >
                Delete
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="container table-responsive">
    <h1 class="text-center text-danger">Deleted Records</h1>
<table class="table table-bordered">
    <thead class="table-danger">
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Address</th>
            <th>Phone</th><th>Gender</th><th>Level</th><th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($deleted as $d): ?>
            <tr>
                <td><?= $d['id'] ?></td>
                <td><?= $d['name'] ?></td>
                <td><?= $d['email'] ?></td>
                <td><?= $d['address'] ?></td>
                <td><?= $d['phone'] ?></td>
                <td><?= $d['gender'] ?></td>
                <td><?= $d['level'] ?></td>
                
                <td>
                    <button 
                    class="btn btn-success btn-sm open-restore-modal"
                    data-id="<?= $d['id'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#restoreConfirmModal"
                    >
                    Restore
                    </button> 
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>


<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5>Edit Record</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="edit-form">
          <input type="hidden" id="edit-id">

          <div class="mb-3">
            <label>Name:</label>
            <input type="text" id="edit-name" class="form-control">
          </div>

          <div class="mb-3">
            <label>Email:</label>
            <input type="email" id="edit-email" class="form-control">
          </div>

          <div class="mb-3">
            <label>Address:</label>
            <input type="text" id="edit-address" class="form-control">
          </div>

          <div class="mb-3">
            <label>Phone:</label>
            <input type="tel" id="edit-phone" class="form-control">
          </div>

          <div class="mb-3">
            <label>Gender:</label><br>
            <input type="radio" name="edit-gender" id="edit-male" value="male"> Male
            <input type="radio" name="edit-gender" id="edit-female" value="female"> Female
          </div>

          <div class="mb-3">
            <label>Level:</label>
            <select id="edit-level" class="form-control">
              <option value="">Select Level</option>
              <option value="1">One</option>
              <option value="2">Two</option>
              <option value="3">Three</option>
            </select>
          </div>

          <button id="ajax-update" class="btn btn-dark mt-3">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="deleteConfirmModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="fw-bold">Are you sure you want to delete this record?</p>
            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <form method="POST">
                    <input type="hidden" name="delete_id" id="delete-id-hidden">
                    <button name="delete" class="btn btn-danger">Delete</button>
                </form>

            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="restoreConfirmModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Confirm Restore</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="fw-bold">Are you sure you want to Restore this record?</p>
            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <form method="POST">
                    <input type="hidden" name="restore_id" id="restore-id-hidden">
                    <button name="restore" class="btn btn-success">Restore</button>
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
