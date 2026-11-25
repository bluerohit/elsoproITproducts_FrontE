<?php
include "config.php";
$title = "Forgot Password | Elso";
?>
<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>

<body class="rbt-header-sticky bg-color-f6">
    <h1 class="visually-hidden">Forgot Password - Elso</h1>
    
    <!-- Common Header Include -->
    <?php include "header.php" ?>

    <!-- Breadcrumb -->
    <?php
    render_breadcrumb([
        ['Home', '/webdemo/index.php'],
        ['Forgot Password', null],
    ]);
    ?>

    <style>
        /* ---- Modern Forgot Password Page Styling ---- */
        .elso-forgot-wrapper {
            padding: 10px 0 90px;
        }

        .elso-auth-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .elso-auth-card h3 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .elso-auth-card p.subtitle-text {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .elso-auth-card .form-group {
            margin-bottom: 20px;
        }

        .elso-auth-card label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .elso-auth-card input.form-control {
            width: 100%;
            height: 50px;
            padding: 0 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            color: #1e293b;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .elso-auth-card input.form-control:focus {
            border-color: var(--color-primary, #2f57ef);
            background: #ffffff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(47, 87, 239, 0.1);
        }

        .elso-auth-btn {
            width: 100%;
            height: 50px;
            background: var(--color-primary, #2f57ef);
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            border: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 16px;
        }

        .elso-auth-btn:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(47, 87, 239, 0.25);
        }

        .auth-footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
            color: #64748b;
        }

        .auth-footer-text a {
            color: var(--color-primary, #2f57ef);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer-text a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="elso-forgot-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-8 col-12">
                    <div class="elso-auth-card">
                        <h3>Reset Password</h3>
                        <p class="subtitle-text">Enter your registered email address below, and we'll send you instructions to reset your password.</p>
                        
                        <form action="#" method="POST">
                            <div class="form-group mb-4">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your registered email" required>
                            </div>

                            <button type="submit" name="reset_btn" class="elso-auth-btn">Send Reset Link</button>

                            <div class="auth-footer-text">
                                Remembered your password? <a href="login">Sign In</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Common Footer Include -->
    <?php include "footer.php" ?>

</body>
</html>