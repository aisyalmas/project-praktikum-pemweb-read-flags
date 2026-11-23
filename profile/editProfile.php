<?php
    require_once __DIR__ ."/../config/config.php";
    require_once __DIR__ ."/../config/koneksi.php";
    require_once __DIR__ ."/../profile/prosesEditProfile.php";

    if(!isset($_SESSION['user_id'])){
        header('location:'.BASE_URL.'/auth/login.php');
        exit;
    }

    //  AMBIL DATA USER DARI DATABASE 
    $user_id=$_SESSION['user_id'];
    $stmt=mysqli_prepare($connect,"SELECT * FROM users WHERE id=?");
    mysqli_stmt_bind_param($stmt,'i',$user_id);
    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);
    $user=mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    //  GENERATE INISIAL UNTUK AVATAR 
    $modalInitial = '';
    foreach (preg_split('/\s+/', trim($user['name'] ?? '')) as $part) {
        if ($part !== '') $modalInitial .= strtoupper(substr($part, 0, 1));
    }
    $modalInitial = substr($modalInitial !== '' ? $modalInitial : '?', 0, 2);
?>

<!--  EDIT PROFILE  -->
<div class="modal fade" id="editProfile" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="<?= BASE_URL ?>/profile/prosesEditProfile.php" method="POST" class="modal-content profile-modal">
            <div class="modal-header border-0 d-flex align-items-center gap-2">
                <button type="button" class="btn btn-light rounded-circle" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h2 class="modal-title profile-modal-title mb-0 mx-auto" id="editProfileModalLabel">edit profile</h2>
                <span style="width: 38px"></span>
            </div>

            <div class="modal-body pt-0">
                <!-- Avatar Display -->
                <div class="profile-modal-avatar mx-auto mb-4"><?= htmlspecialchars($modalInitial) ?></div>

                <!-- Form Input Name -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="inputName" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" placeholder="Name">
                    <label for="inputName">Name</label>
                </div>

                <!-- Form Input Bio -->
                <div class="form-floating mb-3">
                    <textarea class="form-control" id="inputBio" name="bio" placeholder="Bio"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                    <label for="inputBio">Bio</label>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 button-cancel" data-bs-dismiss="modal">cancel</button>
                <button type="submit" class="btn btn-dark rounded-pill px-4">save</button>
            </div>
        </form>
    </div>
</div>
