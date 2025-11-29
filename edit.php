<?php include('config.php');?>


<?php
$record = null;


$id_to_edit = ($_GET['id'] );


try {
    $sql_fetch = "SELECT * FROM student WHERE id = :id";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bindParam(':id',$id_to_edit , PDO::PARAM_INT);
    $stmt_fetch->execute();
    $record = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

    
    
} catch (PDOException $e) {
    die("Database Fetch Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) { 
    
    
    $id_to_update = ($_GET['id'] ); 

    $new_name = htmlspecialchars($_POST['name']);
    $new_email = htmlspecialchars($_POST['email'] );
    $new_address = htmlspecialchars($_POST['address'] );
    $new_phone = htmlspecialchars($_POST['phone'] );
    $new_gender = htmlspecialchars($_POST['gender'] );
    $new_level = htmlspecialchars($_POST['level'] );
    $captcha_verified = (isset($_POST["captcha"]) && $_POST["captcha"] == '1') ? 1 : 0; 
    try {
       
        $sql_update = "
             UPDATE student 
             SET name = :name, 
                 email = :email, 
                 address = :address, 
                 phone = :phone, 
                 gender = :gender, 
                 level = :level, 
                 captcha = :captcha_v
             WHERE id = :id";
             
         $stmt_update = $conn->prepare($sql_update);
         
        
         $stmt_update->bindParam(':name', $new_name);
         $stmt_update->bindParam(':email', $new_email);
         $stmt_update->bindParam(':address', $new_address);
         $stmt_update->bindParam(':phone', $new_phone);
         $stmt_update->bindParam(':gender', $new_gender);
         $stmt_update->bindParam(':level', $new_level);
         $stmt_update->bindParam(':captcha_v', $captcha_verified, PDO::PARAM_INT);
         $stmt_update->bindParam(':id', $id_to_update, PDO::PARAM_INT);
        
        $stmt_update->execute();
        $message = "<p class='alert alert-success mt-3'>  Data for $new_name added .</p>";
        
       
        header("Location: index.php?update_msg=data updated...");
        exit();

    } catch (PDOException $e) {
      
        $message = "<p style='color: red;'> Update Error: " . $e->getMessage() . "</p>";
        
        $record = [
            'id' => $id_to_update,
            'name' => $new_name,
            'email' => $new_email,
            'address' => $new_address,
            'phone' => $new_phone,
            'gender' => $new_gender,
            'level' => $new_level,
            'captcha' => $captcha_verified,
            
        ];
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
        
        <?php echo $message;?>

        <form action="edit.php?id=<?php echo htmlspecialchars($id_to_edit); ?>" class="form-container px-4 py-1 text-white" method="post" enctype="multipart/form-data">
            <div class="mb-3 mt-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control custom-input" id="name" name="name" value="<?php echo htmlspecialchars($record['name']); ?>" required >
            </div>
            <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control custom-input" id="email" name="email" value="<?php echo htmlspecialchars($record['email']); ?>" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control custom-input" id="address" name="address" value="<?php echo htmlspecialchars($record['address']); ?>" required>
            </div>
            <div class="mb-3 mt-3 d-flex flex-column">
                <label for="phone" class="form-label">Phone Number:</label>
                <input type="tel" class="form-control custom-input" id="phone" name="phone" value="<?php echo htmlspecialchars($record['phone']); ?>" required>
            </div>
            <div class="mb-4">
                <label class=" d-block" >Gender</label>
                <div class="d-flex">
                    <div class="form-check me-5">
                        <input class="form-check-input " type="radio" name="gender" id="male" value="male" <?php if ($record['gender'] == 'male') echo 'checked'; ?> >
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" <?php if ($record['gender'] == 'female') echo 'checked'; ?> >
                        <label class="form-check-label " for="female">Female</label>
                    </div>
                </div>
            </div>

            <label for="studies" class="form-label">Level of studies</label>
            <div class="mb-4">
                <select class="form-select custom-input" aria-label="Default select example" name="level"  required>
                    <option value="" selected>Open this select menu</option>
                    <option value="1" <?php if ($record['level'] == '1') echo 'selected'; ?>>One</option>
                    <option value="2" <?php if ($record['level'] == '2') echo 'selected'; ?>>Two</option>
                    <option value="3" <?php if ($record['level'] == '3') echo 'selected'; ?>>Three</option>
                    
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
                        <input class="form-check-input" type="checkbox" id="robotCheck" name="captcha" value="1" <?php if ($record['captcha'] == '1') echo 'checked'; ?>>
                        <label class="form-check-label text-dark" for="robotCheck"><p>I'm not a robot</p></label>
                    </div>
                    <div class="captcha-img-placeholder">
                        <img src="img/recaptcha-icon.svg " alt="reCAPTCHA" class="img-fluid" width="40px">
                    </div>
                </div>
            </div>

            <div class="mb-5 pb-4">
                <button type="submit" class="btn custom-submit-btn float-end mt-3" name="update">UPDATE</button>
            </div>
        </form>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.1.0/build/js/intlTelInput.min.js"></script>
    <script src="script.js"></script>
</body>
</html>


