
var aoUserParams = {
    poo: 1,
    doo: {
        laa: 2,
        baa: "poulet",
        caa: "poulet",
        daa: {
            mii: 123,
            dee: 123
        }
    }
};
var aoParams = {
    poo: 1,
    doo: {
        laa: 1,
        baa: "poulet",
        daa: {
            zee: 123,
            dee: 123
        }
    }
};
console.log(Bee.arrayReplace(aoParams, aoUserParams, ['doo', 'doo.daa']));