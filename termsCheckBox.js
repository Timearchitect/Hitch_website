let checkbox = document.getElementById("termsCheckbox");
checkbox.addEventListener("change", (e) => {
    let submitBtn = document.getElementById("signup-button");
   // console.log(submitBtn);
    submitBtn.disabled = !checkbox.checked;
    //console.log("ticked");
});
