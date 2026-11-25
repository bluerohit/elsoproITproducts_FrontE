<?php
include "config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title = "Upload BOQ | Elso";
?>

<!DOCTYPE html>
<html lang="en">

<?php include "head.php"; ?>

<body class="rbt-header-sticky">

    <h1 class="visually-hidden">Upload BOQ</h1>

    <?php include "header.php"; ?>

    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['Upload BOQ', null],
    ]);
    ?>

    <style>
        .boq-upload-area {
            background: #f8f9fb;
            padding: 70px 0;
        }

        .boq-form-wrapper {
            background: #fff;
            border-radius: 10px;
            padding: 35px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.06);
        }

        .boq-form-wrapper h3 {
            margin-bottom: 10px;
            font-size: 28px;
        }

        .boq-form-wrapper .form-label {
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }

        .boq-form-wrapper .form-control {
            min-height: 48px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 14px;
            font-size: 14px;
        }

        .boq-form-wrapper textarea.form-control {
            min-height: 110px;
        }

        .boq-file-drop {
            border: 2px dashed #c8d5e5;
            border-radius: 8px;
            padding: 35px 20px;
            text-align: center;
            background: #fafcff;
            cursor: pointer;
            transition: 0.3s;
        }

        .boq-file-drop:hover {
            border-color: #2455d6;
            background: #f2f6ff;
        }

        .boq-file-drop i {
            font-size: 42px;
            color: #2455d6;
            margin-bottom: 12px;
        }

        .boq-file-drop p {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .boq-file-drop small {
            color: #777;
        }

        #boqFileList {
            margin-top: 15px;
            text-align: left;
        }

        .boq-file-item {
            background: #f4f6f9;
            padding: 9px 12px;
            border-radius: 5px;
            margin-top: 7px;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .boq-submit-btn {
            background: #2455d6;
            color: #fff;
            border: none;
            padding: 14px 35px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
        }

        .boq-submit-btn:hover {
            background: #183fa8;
            color: #fff;
        }

        .boq-required {
            color: #dc3545;
        }

        @media(max-width: 767px) {
            .boq-form-wrapper {
                padding: 20px;
            }

            .boq-upload-area {
                padding: 40px 0;
            }
        }
    </style>

    <section class="boq-upload-area">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="boq-form-wrapper">

                        <div class="text-center mb-4">
                            <h3>Upload Your BOQ</h3>
                            <!-- <p>
                                Share your Bill of Quantity with us.
                                Our team will review your requirements and contact you.
                            </p> -->
                        </div>

                        <?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>

                            <!-- Added 'boq-message' class here -->
                            <div class="alert alert-success boq-message">
                                Your BOQ has been uploaded successfully!
                            </div>

                        <?php } elseif (!empty($_GET['error'])) { ?>

                            <!-- Added 'boq-message' class here -->
                            <div class="alert alert-danger boq-message">
                                <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>

                        <?php } ?>

                        <form action="save-boq.php" method="POST" enctype="multipart/form-data">

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Full Name <span class="boq-required">*</span>
                                    </label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Enter your full name" maxlength="150" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Company Name
                                    </label>
                                    <input type="text" name="company_name" class="form-control"
                                        placeholder="Enter company name" maxlength="200">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Email Address <span class="boq-required">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Enter email address" maxlength="150" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Mobile Number <span class="boq-required">*</span>
                                    </label>
                                    <input type="tel" name="mobile" class="form-control"
                                        placeholder="Enter mobile number" maxlength="20" required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        Upload BOQ Files
                                        <span class="boq-required">*</span>
                                    </label>

                                    <label for="boq_files" class="boq-file-drop d-block">
                                        <i class="feather-upload-cloud"></i>
                                        <p>Click here to upload or drag and drop files</p>
                                        <small>
                                            JPG, JPEG, PNG, WEBP, PDF, XLS, XLSX, DOC, DOCX
                                        </small>
                                        <br>
                                        <small>Maximum 10 files, 15 MB per file</small>
                                    </label>

                                    <input type="file" name="boq_files[]" id="boq_files" class="d-none"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf,.xls,.xlsx,.doc,.docx" multiple required>

                                    <div id="boqFileList"></div>
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label">
                                        Description / Remarks
                                    </label>
                                    <textarea name="remarks" class="form-control"
                                        placeholder="Enter project requirements or additional details"></textarea>
                                </div>

                                <div class="col-12 text-center">
                                    <button type="submit" class="boq-submit-btn">
                                        Submit BOQ
                                        <i class="feather-arrow-right"></i>
                                    </button>
                                </div>

                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <script>
        const boqInput = document.getElementById('boq_files');
        const boqFileList = document.getElementById('boqFileList');

        const allowedExtensions = [
            'jpg', 'jpeg', 'png', 'webp',
            'pdf', 'xls', 'xlsx', 'doc', 'docx'
        ];

        boqInput.addEventListener('change', function () {
            boqFileList.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length > 10) {
                alert('You can upload maximum 10 files.');
                this.value = '';
                return;
            }

            let totalSize = 0;

            for (let file of files) {
                const extension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(extension)) {
                    alert('Invalid file format: ' + file.name);
                    this.value = '';
                    boqFileList.innerHTML = '';
                    return;
                }

                if (file.size > 15 * 1024 * 1024) {
                    alert(file.name + ' exceeds 15 MB limit.');
                    this.value = '';
                    boqFileList.innerHTML = '';
                    return;
                }

                totalSize += file.size;

                const item = document.createElement('div');
                item.className = 'boq-file-item';

                const name = document.createElement('span');
                name.textContent = file.name;

                const size = document.createElement('span');
                size.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

                item.appendChild(name);
                item.appendChild(size);

                boqFileList.appendChild(item);
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const messages = document.querySelectorAll(".boq-message");

            messages.forEach(function (message) {
                setTimeout(function () {
                    message.style.transition = "opacity 0.5s ease";
                    message.style.opacity = "0";

                    setTimeout(function () {
                        message.remove();
                    }, 500);

                }, 4500); // 4.5 seconds wait + 0.5 second fade transition = total 5 seconds
            });
        });
    </script>

    <?php include "footer.php"; ?>

</body>

</html>