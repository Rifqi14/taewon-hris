@extends('customer.layouts.app')
@section('title', 'Daftar')

@section('content')
<!-- Register -->
<section class="register">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form action="#">
                    <h5>Buat Akun</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <label>Nama Lengkap*</label>
                            <input type="text" name="f-name" placeholder="Nama Lengkap">
                        </div>
                        <div class="col-md-8">
                            <label>Email*</label>
                            <input type="text" name="mail" placeholder="Email">
                        </div>
                        <div class="col-md-8">
                            <label>Telepon*</label>
                            <input type="text" name="phn" placeholder="Telepon">
                        </div>
                        <div class="col-md-8">
                            <label>Password*</label>
                            <input type="text" name="pas" placeholder="Password">
                        </div>
                        <div class="col-md-8">
                            <label>Konfirmasi Password*</label>
                            <input type="text" name="c-pas" placeholder="Konfirmasi Password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div>
                                <input type="checkbox" name="t-box" id="t-box">
                                <label for="t-box">Saya telah membaca dan menyetujui kebijakan privasi.</label>
                            </div>
                            <div>
                                <input type="checkbox" name="c-box" id="c-box">
                                <label for="c-box">Berlangganan</label>
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <button type="button" name="button">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
