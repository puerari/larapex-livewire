<div style="margin-top: 20px;" x-data="{
    id: @entangle('brush_chart_id'),
    code: @entangle('brush_chart_code'),

    mainchart: null,
    main_id: @entangle('main_chart_id'),
    main_code: @entangle('main_chart_code'),
    options_main: @entangle('main_options'),
    isResetMain: false,
    isDestroyMain: false,

    subchart: null,
    sub_id: @entangle('sub_chart_id'),
    sub_code: @entangle('sub_chart_code'),
    options_sub: @entangle('sub_options'),
    isResetSub: false,
    isDestroySub: false,

    redraw: @entangle('redraw'),
    animate: @entangle('animate'),
    updateSyncCharts: @entangle('updateSyncCharts'),
    update: @entangle('update'),
    zoom: @entangle('zoom'),

    themes: @js(config('larapex-livewire.available_themes')),

    initChart() {
        window.addEventListener('themeChanged', (e) => {
            this.changeSubTheme(e.detail.theme);
            this.changeMainTheme(e.detail.theme);
            this.updateOptions();
        });
        if (this.subchart == null) {
            let sub_foptions = {!! $sub_foptions !!};
            this.deepMergeObjects(this.options_sub, sub_foptions);
            this.changeSubTheme(this.options_sub.theme.mode);
            this.subchart = new ApexCharts($refs.chartElemSub, this.options_sub);
            this.subchart.render();
        }
        if (this.mainchart == null) {
            let main_foptions = {!! $main_foptions !!};
            this.deepMergeObjects(this.options_main, main_foptions);
            this.changeMainTheme(this.options_main.theme.mode);
            this.mainchart = new ApexCharts($refs.chartElemMain, this.options_main);
            this.mainchart.render();
        }
    },
    deepMergeObjects(options, mergeObject) {
        let setCallback = function(path, callback) {
            let schema = options; // a moving reference to internal objects within obj
            let pList = path.split('.'); // seperate do natation
            let len = pList.length;
            for (let i = 0; i < len - 1; i++) {
                let elem = pList[i];
                if (!schema[elem]) schema[elem] = {} // key not exists => assign new obj
                schema = schema[elem];
            }
            schema[pList[len - 1]] = callback;
        }
        for (let key in mergeObject) setCallback(key, mergeObject[key]);
    },
    osTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    },
    handleTheme() {
        let subTheme = this.options_sub.theme.mode;
        this.changeSubTheme(subTheme);

        let mainTheme = this.options_main.theme.mode;
        this.changeMainTheme(mainTheme);
    },
    changeSubTheme(theme) {
        this.applySubTheme(!['light', 'dark'].includes(theme) ? this.osTheme() : theme);
    },
    changeMainTheme(theme) {
        this.applyMainTheme(!['light', 'dark'].includes(theme) ? this.osTheme() : theme);
    },
    applyMainTheme(theme) {
        this.options_main.theme.mode = theme;
        this.options_main.chart.foreColor = this.themes[theme]['font_color'];
        this.options_main.chart.background = this.themes[theme]['background_color'];
    },
    applySubTheme(theme) {
        this.options_sub.theme.mode = theme;
        this.options_sub.chart.foreColor = this.themes[theme]['font_color'];
        this.options_sub.chart.background = this.themes[theme]['background_color'];
    },
    updateChartOptions() {
        this.handleTheme();
        this.updateOptions();
    },
    updateOptions() {
        if (this.mainchart !== null && Object.keys(this.options_main).length > 0) {
            this.mainchart.updateOptions(
                this.options_main,
                this.redraw,
                this.animate,
                this.updateSyncCharts
            );
        }
        if (this.subchart !== null && Object.keys(this.options_sub).length > 0) {
            this.subchart.updateOptions(
                this.options_sub,
                this.redraw,
                this.animate,
                this.updateSyncCharts
            );
        }
    },
    updateSeries() {
        this.updateMainSeries();
        this.updateSubSeries();
    },
    updateMainSeries() {
        if (this.mainchart !== null && Object.keys(this.options_main).length > 0) {
            this.mainchart.updateSeries(
                this.options_main.series,
                this.animate
            );
        }
    },
    updateSubSeries() {
        if (this.subchart !== null && Object.keys(this.options_sub).length > 0) {
            this.subchart.updateSeries(
                this.options_sub.series,
                this.animate
            );
        }
    },
    resetChart() {
        this.resetMainChart();
        this.resetSubChart();
    },
    resetMainChart() {
        if (this.mainchart !== null) this.mainchart.resetSeries(
            this.update,
            this.zoom
        );
        this.isResetMain = true;
    },
    resetSubChart() {
        if (this.subchart !== null) this.subchart.resetSeries(
            this.update,
            this.zoom
        );
        this.isResetSub = true;
    },
    destroyChart() {
        if (this.mainchart !== null) {
            this.mainchart.destroy();
            this.isDestroyMain = true;
        }
        if (this.subchart !== null) {
            this.subchart.destroy();
            this.isDestroySub = true;
        }
    }
}" x-id="['apex_chart_brush','apex_chart_brush_main','apex_chart_brush_sub']"
    x-init="initChart();
    $watch('options_main', (newOptions) => {
        if (mainchart && (isResetMain !== true) && (isDestroyMain !== true) && JSON.stringify(mainchart.opts) !== JSON.stringify(newOptions)) {
            updateMainSeries();
        }
        isResetMain = true;
    });
    $watch('options_sub', (newOptions) => {
        if (subchart && (isResetSub !== true) && (isDestroySub !== true) && JSON.stringify(subchart.opts) !== JSON.stringify(newOptions)) {
            updateSubSeries();
        }
        isResetSub = true;
    });" x-on:update:chart:options="updateChartOptions" x-on:reset:chart="resetChart" x-on:delete:chart="destroyChart"
    wire:ignore>
    <div :class="'apex-brush-wrapper-' + id">
        <div :id="$id('apex_chart_brush_sub')" x-ref="chartElemSub" style="position: relative; margin-top: -38px;"></div>
        <div :id="$id('apex_chart_brush_main')" x-ref="chartElemMain"></div>
    </div>
</div>
