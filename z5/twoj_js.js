function myLoadHeader() {
    $(document).ready(function () {
        $('#myHeader').load("header.php");
    });
}

function logVisitor() {
    const data = new URLSearchParams();
    data.append('screen_res', screen.width + 'x' + screen.height);
    data.append('window_res', window.innerWidth + 'x' + window.innerHeight);
    data.append('colors', screen.colorDepth);
    data.append('cookies', navigator.cookieEnabled ? 1 : 0);
    data.append('java', navigator.javaEnabled() ? 1 : 0);
    data.append('language', navigator.language || navigator.userLanguage);

    fetch('log_visitor.php', {
        method: 'POST',
        body: data
    });
}

window.addEventListener('load', logVisitor);

// function validate_country() {
//     fetch('https://pas.spyte.xyz/z3/block_foreign', {
//         method: 'GET'
//     })
//         .then(function (response) { return response.json(); })
//         .then(function (json) {
//             if (json.allowed === false) {
//                 window.location.replace("https://pas.spyte.xyz/z3/blocked");
//             }
//         });
// }

// window.addEventListener('load', validate_country);
