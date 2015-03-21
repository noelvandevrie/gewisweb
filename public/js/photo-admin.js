/*
 * This script will handle all javascript functions neeeded for the admin
 * pages.
 * Depends: jquery, photo.js
 */

Photo.Admin = {};
Photo.Admin.activePage = 'photo';
Photo.Admin.selectedCount = 0;
Photo.Admin.loadPage = function (resource) {
    $.getJSON(resource, function (data) {
        Photo.Admin.activePage = resource;
        Photo.Admin.selectedCount = 0;
        $("#album").html('<div class="row"></div>');
        $.each(data.albums, function (i, album) {
            href = 'photo/album/' + album.id
            $("#album").append('<div class="col-lg-3 col-md-4 col-xs-6 thumb">'
                    + '<a class="thumbnail" href="' + href + '">'
                    + '<img class="img-responsive" src="/data/photo/' + album.coverPath + '" alt="">'
                    + '<input type="checkbox" class="thumbail-checkbox">'
                    + album.name
                    + '</a>'
                    + '</div>');
        });

        $("#album").find("a").each(function () {
            $(this).on('click', Photo.Admin.albumClicked);
        });

        $.each(data.photos, function (i, photo) {
            href = 'photo/photo/' + photo.id
            $("#album").append('<div class="col-lg-3 col-md-4 col-xs-6 thumb">'
                    + '<a class="thumbnail" href="' + href + '">'
                    + '<img class="img-responsive" src="/data/photo/' + photo.smallThumbPath + '" alt="">'
                    + '<input type="checkbox" class="thumbail-checkbox">'
                    + '</a>'
                    + '</div>');
        });
        $("#paging").html('');

        $.each(data.pages, function (i, page) {
            href = 'photo/album/' + data.album.id + '/' + page;
            if (page === data.activepage)
            {
                $("#paging").append('<li class="active"><a href="' + href + '">' + (page + 1) + '</a></li>');
            } else {
                $("#paging").append('<li><a href="' + href + '">' + (page + 1) + '</a></li>');
            }
        });
        if (data.activepage > 0)
        {
            href = 'photo/album/' + data.album.id + '/' + (data.activepage - 1);
            $("#paging").prepend('<li><a id="previous" href="' + href + '">'
                    + '<span aria-hidden="true">«</span>'
                    + '<span class="sr-only">Previous</span>'
                    + '</a></li>');
        }
        if (data.activepage < data.lastpage) {
            href = 'photo/album/' + data.album.id + '/' + (data.activepage + 1);
            $("#paging").append('<li><a id="next" href="' + href + '">'
                    + '<span aria-hidden="true">»</span>'
                    + '<span class="sr-only">Previous</span>'
                    + '</a></li>');
        }

        $("#paging").find("a").each(function () {
            $(this).on('click', function (e) {
                e.preventDefault();
                Photo.Admin.loadPage(e.target.href);

            });
        });

        $(".thumbail-checkbox").change(Photo.Admin.itemSelected);
        $("#btnAdd").attr('href', 'photo/album/' + data.album.id + '/add');
    });
}

Photo.Admin.regenerateCover = function () {
    $("#coverPreview").empty();
    $("#coverPreview").attr('class', 'spinner');
    $.post(Photo.Admin.activePage + '/cover', function (data) {
        $.getJSON(Photo.Admin.activePage, function (data) {
            $("#coverPreview").attr('class', '');
            $("#coverPreview").html('<img class="cover-preview" src="/data/photo/' + data.album.coverPath + '">');
        });
    });
}


Photo.Admin.init = function () {
    $("#albumControls").hide();
    var COUNT_SPAN = '<span id="remove-count"></span>'
    $("#remove-multiple").html($("#remove-multiple").html().replace('%i', COUNT_SPAN));
    $(".btn-regenerate").on('click', Photo.Admin.regenerateCover);
    //auto load album on hash
    if (location.hash !== "") {
        $(location.hash).click();
        $(location.hash).parent().parent().children().toggle();
    }
}

Photo.Admin.itemSelected = function () {
    if (this.checked) {
        Photo.Admin.selectedCount++;
    } else {
        Photo.Admin.selectedCount--;
    }
    $("#remove-count").html(Photo.Admin.selectedCount);
    if (Photo.Admin.selectedCount > 0)
    {
        $("#remove-multiple").removeClass("btn-hidden");
    } else {
        $("#remove-multiple").addClass("btn-hidden");
    }
}
Photo.Admin.initAdd = function () {
    $("#btnImport").click(function () {
        $.post("import",
                {
                    folder_path: $("#folderInput").val()
                },
        function (data) {
            $("#spinner").hide();
            if (data.success) {
                $("#successAlert").show();
            } else {
                $("#errorAlert").html(data.error);
                $("#errorAlert").show();
                $("#import").show();
            }
        });
        $("#errorAlert").hide();
        $("#spinner").show();
        $("#import").hide();
    });

}
Photo.Admin.updateBreadCrumb = function (target) {

    if (target.attr('class') == 'thumbnail') {
        a = target.clone();
        a.children().remove();
        a.attr('class', '');
        a.on('click', Photo.Admin.albumClicked);
        item = $("<li></li>").append(a);
        $("#breadcrumb").append(item)
    } else if (target.parent().parent().attr('id') == 'breadcrumb') {
        target.parent().nextAll().remove();
    } else {
        $("#breadcrumb").empty();
        while (!target.is('div')) {
            if (target.children('a').length > 0)
            {
                a = target.children('a').clone();
                a.on('click', Photo.Admin.albumClicked);
                item = $("<li></li>").append(a);
                $("#breadcrumb").prepend(item)
            }
            target = target.parent();
        }
    }
}
Photo.Admin.albumClicked = function (e) {
    e.preventDefault();
    //workaround for preventing page from jumping when changing hash
    if (history.pushState) {
        history.pushState(null, null, '#'+$(this).attr('id'));
    }
    else {
        location.hash = $(this).attr('id');
    }

    location.hash = $(this).attr('id');
    $("#albumControls").show();
    Photo.Admin.updateBreadCrumb($(this));
    Photo.Admin.loadPage(e.target.href);

}
$.fn.extend({
    treed: function () {
        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator glyphicon glyphicon-plus-sign'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass("glyphicon-minus-sign glyphicon-plus-sign");
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
        $('.branch .indicator').on('click', function () {
            $(this).closest('li').click();
        });
        //fire event to open branch if the li contains an anchor instead of text
        $('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
                // Photo.Admin.loadPage(e.target.href);

            });
        });
        tree.find("a").each(function () {
            $(this).on('click', Photo.Admin.albumClicked);
        });
    }
});


