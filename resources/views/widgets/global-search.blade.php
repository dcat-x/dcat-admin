<style>
.dcat-global-search {
    position: relative;
    margin-left: 10px;
}
.dcat-global-search .search-input {
    background: rgba(0,0,0,0.05);
    border: 1px solid transparent;
    border-radius: 6px;
    padding: 6px 12px 6px 32px;
    font-size: 13px;
    width: 200px;
    transition: all 0.3s ease;
    outline: none;
    color: inherit;
}
.dcat-global-search .search-input:focus {
    width: 320px;
    background: #fff;
    border-color: #5a8dee;
    box-shadow: 0 2px 8px rgba(90,141,238,0.15);
}
.dcat-global-search .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 14px;
    pointer-events: none;
}
.dcat-global-search .search-shortcut {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    color: #aaa;
    background: rgba(0,0,0,0.06);
    padding: 1px 6px;
    border-radius: 3px;
    pointer-events: none;
    transition: opacity 0.2s;
}
.dcat-global-search .search-input:focus ~ .search-shortcut {
    opacity: 0;
}
.dcat-global-search .search-results {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 380px;
    max-height: 420px;
    overflow-y: auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.15);
    z-index: 9999;
    border: 1px solid #e8e8e8;
}
.dcat-global-search .search-results.active {
    display: block;
}
.dcat-global-search .search-group-title {
    font-size: 11px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    padding: 10px 14px 4px;
    letter-spacing: 0.5px;
}
.dcat-global-search .search-result-item {
    display: flex;
    align-items: center;
    padding: 8px 14px;
    cursor: pointer;
    text-decoration: none;
    color: #333;
    transition: background 0.15s;
}
.dcat-global-search .search-result-item:hover,
.dcat-global-search .search-result-item.active {
    background: #f0f5ff;
    color: #333;
    text-decoration: none;
}
.dcat-global-search .search-result-item .result-icon {
    width: 28px;
    font-size: 14px;
    color: #5a8dee;
    flex-shrink: 0;
}
.dcat-global-search .search-result-item .result-info {
    flex: 1;
    min-width: 0;
}
.dcat-global-search .search-result-item .result-title {
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dcat-global-search .search-result-item .result-desc {
    font-size: 11px;
    color: #999;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dcat-global-search .search-empty {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 13px;
}
.dcat-global-search .search-loading {
    padding: 20px;
    text-align: center;
    color: #999;
    font-size: 13px;
}
</style>

<div class="dcat-global-search">
    <i class="feather icon-search search-icon"></i>
    <input type="text" class="search-input" placeholder="{{ trans('admin.search') }}..." autocomplete="off">
    <span class="search-shortcut">{{ $shortcut }}</span>
    <div class="search-results"></div>
</div>

<script>
(function () {
    var $wrap = $('.dcat-global-search');
    var $input = $wrap.find('.search-input');
    var $results = $wrap.find('.search-results');
    var activeIndex = -1;
    var debounceTimer = null;
    var searchUrl = '{{ admin_url("_global-search") }}';

    function doSearch(keyword) {
        if (!keyword || keyword.length < 1) {
            $results.removeClass('active').empty();
            return;
        }
        $results.addClass('active').html('<div class="search-loading">{{ trans("admin.loading") }}...</div>');
        $.ajax({
            url: searchUrl,
            data: { q: keyword },
            dataType: 'json',
            success: function (resp) {
                renderResults(resp.groups || []);
            },
            error: function () {
                $results.removeClass('active').empty();
            }
        });
    }

    function renderResults(groups) {
        if (!groups.length) {
            $results.html('<div class="search-empty">{{ trans("admin.no_data") }}</div>');
            return;
        }
        var html = '';
        for (var g = 0; g < groups.length; g++) {
            html += '<div class="search-group-title">' + escapeHtml(groups[g].title) + '</div>';
            for (var i = 0; i < groups[g].items.length; i++) {
                var item = groups[g].items[i];
                html += '<a href="' + escapeHtml(item.url) + '" class="search-result-item">';
                html += '<span class="result-icon"><i class="' + escapeHtml(item.icon || 'feather icon-circle') + '"></i></span>';
                html += '<span class="result-info">';
                html += '<div class="result-title">' + escapeHtml(item.title) + '</div>';
                if (item.description) {
                    html += '<div class="result-desc">' + escapeHtml(item.description) + '</div>';
                }
                html += '</span></a>';
            }
        }
        $results.html(html);
        activeIndex = -1;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function navigateResults(dir) {
        var $items = $results.find('.search-result-item');
        if (!$items.length) return;
        activeIndex += dir;
        if (activeIndex < 0) activeIndex = $items.length - 1;
        if (activeIndex >= $items.length) activeIndex = 0;
        $items.removeClass('active');
        $items.eq(activeIndex).addClass('active');
        var el = $items.eq(activeIndex)[0];
        if (el) el.scrollIntoView({ block: 'nearest' });
    }

    $input.on('input', function () {
        clearTimeout(debounceTimer);
        var val = $.trim($(this).val());
        debounceTimer = setTimeout(function () {
            doSearch(val);
        }, 300);
    });

    $input.on('keydown', function (e) {
        if (e.keyCode === 40) { e.preventDefault(); navigateResults(1); }
        else if (e.keyCode === 38) { e.preventDefault(); navigateResults(-1); }
        else if (e.keyCode === 13) {
            e.preventDefault();
            var $active = $results.find('.search-result-item.active');
            if ($active.length) {
                window.location.href = $active.attr('href');
            }
        } else if (e.keyCode === 27) {
            $input.blur();
            $results.removeClass('active').empty();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.dcat-global-search').length) {
            $results.removeClass('active').empty();
        }
    });

    // Keyboard shortcut
    var shortcut = '{{ $shortcut }}'.toLowerCase();
    $(document).on('keydown', function (e) {
        var isCtrlK = shortcut === 'ctrl+k' && (e.ctrlKey || e.metaKey) && e.key === 'k';
        var isCmdK = shortcut === 'cmd+k' && e.metaKey && e.key === 'k';
        if (isCtrlK || isCmdK) {
            e.preventDefault();
            $input.focus().select();
        }
    });
})();
</script>
