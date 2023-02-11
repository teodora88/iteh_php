<?php
    include 'header.php';
?>

<div class='container mt-2'>
    <h1 class='text-center text-dark'>
        Torte za sve prilike
        </h1>
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
            <select onchange="render()" class="form-control" id="kategorije">
                <option value="0">Sve kategorije</option>
            </select>
        </div>
    </div>
    <div id='podaci'>

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    let proizvodi = [];
    let kategorije = [];
    let boje = [];
    $(function () {
        $.getJSON('server/kategorija/read.php').then((res => {
            if (!res.status) {
                alert(res.error);
                return;
            }
            kategorije = res.kolekcija;
            for (let kat of kategorije) {
                $('#kategorije').append(`
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
            boje = res.kolekcija;

        }))
        .then(ucitajProizvode)

    })

    function ucitajProizvode() {
        $.getJSON('server/porudzbina/read.php', (res => {
            if (!res.status) {
                alert(res.error);
                return;
            }
            proizvodi = res.kolekcija || [];
            render();
        }))
    }

    function render() {
        const search = $('#search').val();
        const sort = Number($('#sort').val());
        const kat = Number($('#kategorije').val());
        const niz = proizvodi.filter(element => {
            return (kat == 0 || element.kategorija == kat) && element.naziv.includes(search)
        }).sort((a, b) => {
            return (a.cena > b.cena) ? sort : 0 - sort;
        });
        let red = 0;
        let kolona = 0;
        $('#podaci').html(`<div id='row-${red}' class='row mt-2'></div>`)
        for (let proizvod of niz) {
            if (kolona === 4) {
                kolona = 0;
                red++;
                $('#podaci').append(`<div id='row-${red}' class='row mt-2'></div>`)
            }
            $(`#row-${red}`).append(
                `
                        <div class='col-3 pt-2 bg-white'>
                            <div class="card" >
                                <img class="card-img-top" src="${proizvod.slika}" alt="Card image cap">
                                <div class="card-body">
                                    <h6 class="card-title">Naziv: ${proizvod.naziv}</h6>
                                    <h6 class="card-title">Cena: ${proizvod.cena}</h6>
                                    <h6 class="card-title">Kategorija: ${kategorije.find(element => element.id === proizvod.kategorija).naziv}</h6>
                                    <h6 class="card-title">Ukus: ${ukus.find(element => element.id === proizvod.ukus).naziv}</h6>
                                   <b>Opis:</b>
                                    <p class="card-text">${proizvod.opis}</p>
                                </div>
                                <div class="card-footer ">
                                    <button class='btn btn-danger form-control' onClick="obrisi(${proizvod.id})">Obrisi</button>
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

            proizvodi = proizvodi.filter(element => element.id != id);
            render();
        })
    }
</script>

<?php
    include 'footer.php';
?>