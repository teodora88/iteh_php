<?php
    include 'header.php';
?>

<br>

<div class='container mt-2'>
    <h1 class='text-center text-dark'>
        Porudzbine
        </h1>
        <br><br>
    <div class="row mt-2">
        <div class="col-3">
            <select onchange="render()" class="form-control" id="sort">
                <option value="1">Po ceni rastuce</option>
                <option value="-1">Po ceni opadajuce</option>
            </select>
        </div>
        <div class="col-6">
            <input onchange="render()" class="form-control" type="text" id="search" placeholder="pretrazi">
        </div>
        <div class="col-3">
            <select onchange="render()" class="form-control" id="kategorija">
                <option value="0">Sve kategorije</option>
            </select>
        </div>
    </div>
    <div id='podaci'>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    let torta = [];
    let kategorija = [];
    let ukus = [];
    $(function () {
        $.getJSON('server/kategorija/read.php').then((res => {
            if (!res.status) {
                alert(res.error);
                return;
            }
            kategorija = res.kolekcija;
            for (let kat of kategorija) {
                $('#kategorija').append(`
                <option value="${kat.id}"> ${kat.naziv}</option>
                `)
            }
        }))
        .then(() => {
                return $.getJSON('server/ukus/read.php')

        }).then((res => {
            if (!res.status) {
                alert(res.error);
                return;
            }
            ukus = res.kolekcija;

        }))
        .then(ucitajProizvode)

    })

    function ucitajProizvode() {
        $.getJSON('server/porudzbina/read.php', (res => {
            if (!res.status) {
                alert(res.error);
                return;
            }
            torta = res.kolekcija || [];
            render();
        }))
    }

    function render() {
        const search = $('#search').val();
        const sort = Number($('#sort').val());
        const kat = Number($('#kategorija').val());
        const niz = torta.filter(element => {
            return (kat == 0 || element.kategorija == kat) && element.naziv.includes(search)
        }).sort((a, b) => {
            return (a.cena > b.cena) ? sort : 0 - sort;
        });
        let red = 0;
        let kolona = 0;
        $('#podaci').html(`<div id='row-${red}' class='row mt-2'></div>`)
        for (let torta of niz) {
            if (kolona === 4) {
                kolona = 0;
                red++;
                $('#podaci').append(`<div id='row-${red}' class='row mt-2'></div>`)
            }
            $(`#row-${red}`).append(
                `
                        <div class='col-3 pt-2 bg-white'>
                            <div class="card" >
                                <img class="card-img-top" src="${torta.slika}" alt="Card image cap">
                                <div class="card-body">
                                    <h6 class="card-title">Naziv: ${torta.naziv}</h6>
                                    <h6 class="card-title">Cena: ${torta.cena}</h6>
                                    <h6 class="card-title">Kategorija: ${kategorija.find(element => element.id === torta.kategorija).naziv}</h6>
                                    <h6 class="card-title">Ukus: ${ukus.find(element => element.id === torta.ukus).naziv}</h6>
                                   <b>Opis:</b>
                                    <p class="card-text">${torta.opis}</p>
                                </div>
                                <div class="card-footer ">
                                    <button class='btn btn-danger form-control' onClick="obrisi(${torta.id})">Obrisi</button>
                                </div>
                            </div>
                        </div>
                    `
            )
            kolona++;
        }

    }
    function obrisi(id) {
        id = Number(id);
        $.post('server/porudzbina/delete.php', { id }).then(res => {
            res = JSON.parse(res);
            if (!res.status) {
                alert(res.error);
                return;
            }

            torta = torta.filter(element => element.id != id);
            render();
        })
    }
</script>

<br><br><br><br><br><br><br><br><br>

<?php
    include 'footer.php';
?>