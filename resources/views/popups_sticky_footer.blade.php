@if(read("id") && read("profiletype") <> 2)
    <div class="fixed-action-btn hidden-lg-up sticky-footer">
        <button class="fab bg-danger" onclick="window.scrollTo(0,document.body.scrollHeight);">
            <span class="white" id="checkout-total"></span>
        </button>
    </div>
@endif

@if(false)
    <style>
        * {
            padding: 3px;
        }
        input, select, textarea {
            border: 1px solid green !important;
            background: #dadada !important;
        }
        div {
            border: 1px solid orange !important;
        }
        .row {
            border: 1px solid blue !important;
        }
        div[class^="col-"], div[class*=" col-"] {
            border: 5px solid purple !important;
        }

        table {
            border: 1px solid yellow !important;
        }

        tr {
            border: 1px solid pink !important;
        }

        td {
            border: 1px solid black !important;
        }
    </style>
@endif

