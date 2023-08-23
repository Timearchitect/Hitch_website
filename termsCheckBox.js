let checkbox = document.getElementById("termsCheckbox");
checkbox.addEventListener("change", (e) => {
    let submitBtn = document.getElementById("signup-button");
    // console.log(submitBtn);
    submitBtn.disabled = !checkbox.checked;
    //console.log("ticked");
});

window.addEventListener("scroll", function () {
    if (window.scrollY == 0) {
        document.getElementsByTagName("nav")[0].classList.add("frosted");
    } else {
        document.getElementsByTagName("nav")[0].classList.remove("frosted");
    }
});
