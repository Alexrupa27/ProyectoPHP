    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("passwordModal");
        const btn = document.getElementById("openModal");
        const closeBtn = document.querySelector(".close");

        btn.addEventListener("click", function () {
            modal.style.display = "flex";
        });

        closeBtn.addEventListener("click", function () {
            modal.style.display = "none";
        });

        window.addEventListener("click", function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    });
