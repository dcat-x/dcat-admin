<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Illuminate\Support\Collection;

class FixColumns
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var int
     */
    public $head;

    /**
     * @var int
     */
    public $tail;

    /**
     * @var Collection
     */
    protected $left;

    /**
     * @var Collection
     */
    protected $right;

    /**
     * @var Collection
     */
    protected $complexLeft;

    /**
     * @var Collection
     */
    protected $complexRight;

    /**
     * @var string
     */
    protected $view = 'admin::grid.fixed-table';

    /**
     * @var int
     */
    protected $height;

    /**
     * FixColumns constructor.
     *
     * @param  int  $head
     * @param  int  $tail
     */
    public function __construct(Grid $grid, $head, $tail = -1)
    {
        $this->grid = $grid;
        $this->head = $head;
        $this->tail = $tail;

        $this->left = Collection::make();
        $this->right = Collection::make();
        $this->complexLeft = Collection::make();
        $this->complexRight = Collection::make();
    }

    /**
     * @return Collection
     */
    public function leftColumns()
    {
        return $this->left;
    }

    /**
     * @return Collection
     */
    public function rightColumns()
    {
        return $this->right;
    }

    /**
     * @return Collection
     */
    public function leftComplexColumns()
    {
        return $this->complexLeft;
    }

    /**
     * @return Collection
     */
    public function rightComplexColumns()
    {
        return $this->complexRight;
    }

    /**
     * @param  int  $height  px
     * @return $this
     */
    public function height(int $height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return \Closure
     */
    public function apply()
    {
        $this->grid->view($this->view);
        $this->grid->with(['tableHeight' => $this->height]);

        $complexHeaders = $this->grid->getVisibleComplexHeaders();

        if ($this->head > 0) {
            if ($complexHeaders) {
                $this->complexLeft = $complexHeaders->slice(0, $this->head);
                $this->left = $this->formatColumns($this->complexLeft);
            } else {
                $this->left = $this->grid->getVisibleColumns()->slice(0, $this->head);
            }
        }

        if ($this->tail < 0) {
            if ($complexHeaders) {
                $this->complexRight = $complexHeaders->slice($this->tail);
                $this->right = $this->formatColumns($this->complexRight);
            } else {
                $this->right = $this->grid->getVisibleColumns()->slice($this->tail);
            }
        }

        $this->addStyle();
        $this->addScript();
    }

    protected function formatColumns(Collection $complexHeaders)
    {
        return $complexHeaders
            ->map(function (ComplexHeader $header) {
                return $header->getColumnNames()->toArray();
            })
            ->flatten()
            ->filter()
            ->map(function ($name) {
                return $this->grid->allColumns()->get($name);
            });
    }

    /**
     * @return $this
     */
    protected function addScript()
    {
        $script = <<<'JS'
(function () {
    var $container = $('.tables-container');
    var $tableMain = $container.find('.table-main');
    var $tableFixed = $container.find('.table-fixed');
    var $fixedLeft = $container.find('.table-fixed-left');
    var $fixedRight = $container.find('.table-fixed-right');
    var minHeight = 600;

    function syncRowHeights() {
        var theadHeight = $tableMain.find('thead tr').outerHeight();
        $tableFixed.find('thead tr').outerHeight(theadHeight);

        var tfootHeight = $tableMain.find('tfoot tr').outerHeight();
        $tableFixed.find('tfoot tr').outerHeight(tfootHeight);

        $tableMain.find('tbody tr').each(function (i) {
            var h = $(this).outerHeight();
            $fixedLeft.find('tbody tr').eq(i).outerHeight(h);
            $fixedRight.find('tbody tr').eq(i).outerHeight(h);
        });
    }

    function updateLayout() {
        var noOverflow = $tableMain.width() >= $tableMain.prop('scrollWidth');

        if (noOverflow || $(window).width() < 600) {
            $tableFixed.hide();
            $tableMain.off('scroll.fixColumns');
            return;
        }

        syncRowHeights();

        var height = Math.max($(window).height() - 220, minHeight);

        $tableMain.each(function () {
            var $this = $(this);
            var tableHeight = $this.find('.custom-data-table.table').eq(0).height();
            var maxHeight = $this.data('height') || Math.min(height, tableHeight);

            $this.css('max-height', maxHeight + 'px');

            if (maxHeight < tableHeight) {
                $this.parents('.tables-container').find('.table-fixed-right').css({right: '12px'});
            }
        });

        $fixedLeft.add($fixedRight).each(function () {
            $(this).css('max-height', (($(this).data('height') || height) - 15) + 'px');
        });

        $tableMain.off('scroll.fixColumns').on('scroll.fixColumns', function () {
            var scrollTop = $(this).scrollTop();
            $(this).parents('.tables-container').find('.table-fixed-right,.table-fixed-left')
                .scrollTop(scrollTop);
        });

        $tableFixed.show();
    }

    updateLayout();

    $(window).on('resize', debounce(updateLayout, 150));

    $container.on('mouseenter', '.table-wrap tbody tr', function () {
        var index = $(this).index();
        $container.find('.table-wrap tbody tr:nth-child(' + (index + 1) + ')').addClass('active');
    }).on('mouseleave', '.table-wrap tbody tr', function () {
        var index = $(this).index();
        $container.find('.table-wrap tbody tr:nth-child(' + (index + 1) + ')').removeClass('active');
    });
})();
JS;

        Admin::script($script, true);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addStyle()
    {
        $style = <<<'CSS'
.tables-container {
    position:relative;
    margin-top: 12px;
}

.tables-container table {
    margin-bottom: 0 !important;
}

.tables-container table th, .tables-container table td {
    white-space:nowrap;
}

.table-wrap table tr .active {
    background: #f5f5f5;
}

.table-main {
    overflow: auto;
    width: 100%;
}

.table-fixed {
    position:absolute;
	top: 0;
	z-index:10;
	overflow: hidden;
}

.table-fixed th {
    background: #eff3f8;
}

.table-fixed-left {
	left:0;
}

.table-fixed-right {
	right:0;
}

.table-fixed-left {
    box-shadow: 5px 0 5px -5px rgba(0,0,0,.1);
}

.table-fixed-right {
    box-shadow: -5px 0 5px -5px rgba(0,0,0,.1);
}

.tables-container .table.table-bordered.dataTable.complex-headers {
    margin-top: 0!important;
}

body.dark-mode .table-fixed-left .table {
     padding: 0 0 0 1rem;
}
body.dark-mode .table-fixed-right .table {
     padding: 0 1rem 0 0;
}
CSS;

        Admin::style($style);

        return $this;
    }
}
