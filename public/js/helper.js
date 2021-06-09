
var waitingDialog = function (d) {
    var a = d(`<div class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:visible;">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="margin:0;"></h5>
                </div>
                <div class="modal-body">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>`);
    return {
        show: function (s, i) {
            var e = d.extend({
                dialogSize: "m",
                progressType: ""
            }, i);
            "undefined" == typeof s && (s = "Loading"), "undefined" == typeof i && (i = {}), 
            a.find(".modal-dialog").attr("class", "modal-dialog").addClass("modal-" + e.dialogSize), 
            a.find(".progress-bar").attr("class", "progress-bar progress-bar-striped progress-bar-animated bg-success"),
            e.progressType && a.find(".progress-bar").addClass("progress-bar-" + e.progressType), 
            a.find("h5").text(s), a.modal()
        },
        hide: function () {
            a.modal("hide")
        }
    }
}(jQuery)