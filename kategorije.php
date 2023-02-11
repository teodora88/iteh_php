<?php
    include 'header.php';
?>

<div class='container mt-2'>
    <h1 class='text-center text-dark'>
        Kategorije mogućih torti
    </h1>
</div>

<div class='container'>
    <div class='row mt-2'>
        <div class='col-6'>
            <table class='table table-dark'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Naziv</th>
                        <th>Izmeni</th>
                        <th>Obrisi</th>
                    </tr>
                </thead>
                <tbody id='kategorije'>

                </tbody>
            </table>


        </div>
        <div class='col-6'>
            <h3 class="text-dark text-centar" id='naslov'>Kreiraj kategoriju</h3>
            <form id='forma'>
                <div class='form-group'>
                    <label for="naziv">Naziv</label>
                    <input required class="form-control" type="text" id="naziv">
                </div>
                <button class="btn btn-dark form-control" type="submit">Sačuvaj</button>

            </form>
            <button id="vrati" hidden class="btn btn-secondary form-control mt-2" onclick="setIndex(-1)">Nazad
            </button>
        </div>
    </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    let kategorije = [];
    let selIndex = -1;

    $(function () {
        ucitajKategorije();
        $('#forma').submit(e => {
            e.preventDefault();
            const naziv = $('#naziv').val();
            if (selIndex === -1) {
                $.post('server/kategorija/create.php', { naziv }).then(res => {
                    res = JSON.parse(res);
                    if (!res.status) {
                        alert(res.error);
                    } else {
                        ucitajKategorije();
                    }
                })
            } else {
                $.post('server/kategorija/update.php', { naziv, id: kategorije[selIndex].id }).then(res => {
                    res = JSON.parse(res);
                    if (!res.status) {
                        alert(res.error);
                    } else {
                        setKategorije(kategorije.map((element, index) => {
                            if (index !== selIndex) {
                                return element;
                            }
                            return { id: element.id, naziv };
                        }));
                        setIndex(-1);
                    }
                })
            }
        })
    })
    function ucitajKategorije() {
        $.getJSON('server/kategorija/read.php').then(res => {

            if (!res.status) {
                alert(res.error);
                return;
            }
            setKategorije(res.kolekcija);
        })
    }
    function obrisi(id) {
        $.post('server/kategorija/delete.php', { id }).then((res) => {
            res = JSON.parse(res);
            if (!res.status) {
                alert(res.error);
                return;
            }
            setKategorije(kategorije.filter((e) => e.id != id));

            setIndex(-1);
        })
    }
    function setKategorije(val) {
        kategorije = val;
        $('#kategorije').html('');

        let index = 0;
        for (let kategorija of kategorije) {
            $('#kategorije').append(`
                    <tr>
                        <td>${kategorija.id}</td>
                        <td>${kategorija.naziv}</td>
                        <td>
                            <button class='btn btn-light form-control' onClick="setIndex(${index})" >Izmeni</button>
                        </td>
                        <td>
                            <button class='btn btn-danger form-control' onClick="obrisi(${kategorija.id})">Obrisi</button>
                        </td>
                    </tr>
                `);
            index++;
        }
    }
    function setIndex(val) {
        selIndex = val
        if (selIndex === -1) {
            $('#naslov').html('Kreiraj kategoriju');
            $('#naziv').val('');

        } else {
            $('#naslov').html('Izmeni kategoriju')
            $('#naziv').val(kategorije[selIndex].naziv);
        }
        $('#vrati').attr('hidden', selIndex === -1)
    }
</script>
<?php
    include 'footer.php';
?>