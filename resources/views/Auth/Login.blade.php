<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/style.css" />
  </head>
  <body>
    <div
      class="container-fluid vh-100 d-flex justify-content-center align-items-center"
    >
      <div class="row" style="max-width: 1200px; width: 100%">
        <!-- Form Section -->
        <div class="col-md-6 d-flex justify-content-center align-items-center">
          <form action="{{ route('login') }}" method="POST" class="w-75">
              @csrf
            <div class="text-center mb-4">
              <h2 class="mb-3">Miresevini</h2>
              <span class="text-secondary">Identifikohu për të vazhduar</span>
            </div>

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <div class="mb-3">
              <label for="phone" class="frm-label">Numri Telefonit Tuaj</label>
              <input
                type="phone"
                class="form-control"
                id="phone"
                name="phone"
                placeholder="Shkruani numrin e telefonit"
              />
            </div>
            <div class="mb-3">
              <label for="password" class="frm-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" />
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input
                  type="checkbox"
                  class="form-check-input"
                  id="rememberMe"
                />
                <label class="form-check-label" for="rememberMe"
                  >Remember me</label
                >
              </div>
              <div>
                <a href="#" class="text-decoration-none">Forgot Password?</a>
              </div>
            </div>
            <button
              type="submit"
              class="btn btn-dark w-100"
              style="color: white; background-color: black"
            >
              Login
            </button>
          </form>
        </div>
        <!-- Image Section -->
        <div
          class="col-md-6 d-none d-md-block d-flex align-items-center justify-content-center"
        >
          <img
            src="assets/img/aside-img.png"
            alt="aside-img"
            class="img-fluid"
            style="max-height: 100vh; width: auto"
          />
        </div>
      </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
