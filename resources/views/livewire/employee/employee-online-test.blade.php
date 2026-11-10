<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
    id="employee-exam-root"
    {{-- Tipe soal aktif dibaca ulang dari DOM oleh proctoring: soal uraian boleh pindah tab / Esc (minimize) --}}
    data-current-question-type="{{ $currentQuestion['question_type'] ?? '' }}"
    {{-- Angka yang berubah setiap Livewire re-render ditaruh di data-* agar atribut x-data tidak ikut berubah.
         Kalau x-data berubah, Alpine re-init dan seluruh listener anti-cheat hilang. --}}
    data-time-remaining="{{ (int) $timeRemainingSeconds }}"
    data-violations="{{ (int) ($violationsCount ?? 0) }}"
    x-data="onlineTestEmployee({
        testId: {{ (int) ($test->id ?? 0) }},
        testState: @entangle('testState'),
        selectedPkg: @entangle('selectedPkg'),
        showConfirmModal: @entangle('showConfirmModal')
    })">

    <script>
    if (typeof window.onlineTestEmployee !== 'function') {
        window.onlineTestEmployee = function(config) {
            return {
                timeRemaining: 0, // diisi dari data-time-remaining saat init
                testId: config.testId,
                testState: config.testState || 'intro',
                selectedPkg: config.selectedPkg,
                timerInterval: null,
                showConfirmModal: config.showConfirmModal,
                isAutoSubmitting: false,
                autoSubmitTimeout: null,
                isGracePeriod: true,
                packages: [
                    { id: 1, name: 'PAKET 1', questions: 100, duration: 240, passPct: 61, minCorrect: 61 },
                    { id: 2, name: 'PAKET 2', questions: 75, duration: 180, passPct: 61, minCorrect: 46 },
                    { id: 3, name: 'PAKET 3', questions: 50, duration: 120, passPct: 61, minCorrect: 31 },
                    { id: 4, name: 'PAKET 4', questions: 25, duration: 60, passPct: 61, minCorrect: 16 }
                ],
                showCalc: false,
                showNotes: false,
                showExcel: false,
                activeTool: 'calc',
                bringToFront(tool) { this.activeTool = tool; },
                toolZ(tool) { return this.activeTool === tool ? 75 : 65; },
                toggleTool(tool) {
                    if (tool === 'excel') {
                        this.showExcel = !this.showExcel;
                        if (this.showExcel) this.bringToFront('excel');
                    } else if (tool === 'calc') {
                        this.showCalc = !this.showCalc;
                        if (this.showCalc) this.bringToFront('calc');
                    } else if (tool === 'notes') {
                        this.showNotes = !this.showNotes;
                        if (this.showNotes) this.bringToFront('notes');
                    }
                },
                closeTool(tool) {
                    if (tool === 'excel') this.showExcel = false;
                    if (tool === 'calc') this.showCalc = false;
                    if (tool === 'notes') this.showNotes = false;
                },
                winPos: (function() {
                    let w = typeof window !== 'undefined' ? window.innerWidth : 1200;
                    let h = typeof window !== 'undefined' ? window.innerHeight : 800;
                    try {
                        let saved = JSON.parse(localStorage.getItem('employee_exam_tools_pos') || 'null');
                        if (saved && saved.calc && saved.notes && saved.excel) {
                            // Abaikan posisi tersimpan yang sudah di luar layar agar jendela tetap bisa ditutup/digeser
                            let onScreen = ['calc', 'notes', 'excel'].every(function(t) {
                                return saved[t] && saved[t].x >= 0 && saved[t].x < w - 50 && saved[t].y >= 0 && saved[t].y < h - 50;
                            });
                            if (onScreen) return saved;
                        }
                    } catch(e) {}
                    return {
                        calc: { x: Math.max(20, w - 340), y: 80 },
                        notes: { x: Math.max(10, w - 430), y: 220 },
                        excel: { x: Math.max(10, Math.floor((w - 680) / 2)), y: 110 }
                    };
                })(),
                winSize: (function() {
                    let w = typeof window !== 'undefined' ? window.innerWidth : 1200;
                    let h = typeof window !== 'undefined' ? window.innerHeight : 800;
                    try {
                        let saved = JSON.parse(localStorage.getItem('employee_exam_tools_size') || 'null');
                        if (saved && saved.calc && saved.excel && saved.notes
                            && typeof saved.calc.w === 'number' && typeof saved.excel.w === 'number' && typeof saved.notes.w === 'number') {
                            saved.calc.w = Math.max(310, saved.calc.w);
                            saved.calc.h = Math.max(440, saved.calc.h);
                            return saved;
                        }
                    } catch(e) {}
                    return {
                        calc: { w: 320, h: 440 },
                        excel: { w: Math.min(740, Math.max(480, w - 80)), h: Math.min(480, Math.max(340, h - 160)) },
                        notes: { w: 360, h: 360 }
                    };
                })(),
                startResize(e, tool) {
                    if (e.button && e.button !== 0) return;
                    if (e.cancelable) e.preventDefault();
                    this.bringToFront(tool);

                    let winEl = document.querySelector('[data-floating-tool="' + tool + '"]');
                    if (!winEl) return;

                    let isTouch = e.type && e.type.startsWith('touch');
                    let startX = isTouch ? (e.touches && e.touches[0] ? e.touches[0].clientX : 0) : e.clientX;
                    let startY = isTouch ? (e.touches && e.touches[0] ? e.touches[0].clientY : 0) : e.clientY;

                    let rect = winEl.getBoundingClientRect();
                    let startW = rect.width;
                    let startH = rect.height;

                    let limits = {
                        calc:  { minW: 310, maxW: Math.min(520, window.innerWidth - 20), minH: 440, maxH: Math.min(720, window.innerHeight - 30) },
                        excel: { minW: 400, maxW: Math.min(1250, window.innerWidth - 20), minH: 260, maxH: Math.min(850, window.innerHeight - 30) },
                        notes: { minW: 280, maxW: Math.min(720, window.innerWidth - 20), minH: 240, maxH: Math.min(800, window.innerHeight - 30) }
                    }[tool] || { minW: 250, maxW: 800, minH: 250, maxH: 600 };

                    document.body.style.userSelect = 'none';
                    document.body.style.webkitUserSelect = 'none';
                    document.body.style.cursor = 'se-resize';

                    const onResizeMove = (ev) => {
                        if (ev.cancelable) ev.preventDefault();
                        let curX = ev.type && ev.type.startsWith('touch') ? (ev.touches && ev.touches[0] ? ev.touches[0].clientX : 0) : ev.clientX;
                        let curY = ev.type && ev.type.startsWith('touch') ? (ev.touches && ev.touches[0] ? ev.touches[0].clientY : 0) : ev.clientY;

                        let newW = Math.max(limits.minW, Math.min(limits.maxW, Math.round(startW + (curX - startX))));
                        let newH = Math.max(limits.minH, Math.min(limits.maxH, Math.round(startH + (curY - startY))));

                        this.winSize[tool].w = newW;
                        this.winSize[tool].h = newH;
                        winEl.style.width = newW + 'px';
                        winEl.style.height = newH + 'px';
                    };

                    const onResizeUp = () => {
                        window.removeEventListener('mousemove', onResizeMove);
                        window.removeEventListener('mouseup', onResizeUp);
                        window.removeEventListener('touchmove', onResizeMove);
                        window.removeEventListener('touchend', onResizeUp);
                        document.body.style.userSelect = '';
                        document.body.style.webkitUserSelect = '';
                        document.body.style.cursor = '';
                        try { localStorage.setItem('employee_exam_tools_size', JSON.stringify(this.winSize)); } catch(err) {}
                    };

                    window.addEventListener('mousemove', onResizeMove, { passive: false });
                    window.addEventListener('mouseup', onResizeUp);
                    window.addEventListener('touchmove', onResizeMove, { passive: false });
                    window.addEventListener('touchend', onResizeUp);
                },
                resetSize(tool) {
                    let defaults = {
                        calc: { w: 320, h: 440 },
                        excel: { w: 740, h: 480 },
                        notes: { w: 360, h: 360 }
                    };
                    if (!defaults[tool]) return;

                    this.winSize[tool] = Object.assign({}, defaults[tool]);
                    let winEl = document.querySelector('[data-floating-tool="' + tool + '"]');
                    if (winEl) {
                        winEl.style.width = defaults[tool].w + 'px';
                        winEl.style.height = defaults[tool].h + 'px';
                    }
                    try { localStorage.setItem('employee_exam_tools_size', JSON.stringify(this.winSize)); } catch(err) {}
                },
                startDrag(e, tool) {
                    if (e.target.closest('button, input, textarea, a, select')) return;
                    if (e.button && e.button !== 0) return;
                    if (e.cancelable) e.preventDefault();
                    this.bringToFront(tool);

                    let winEl = document.querySelector('[data-floating-tool="' + tool + '"]');
                    if (!winEl) return;

                    let isTouch = e.type && e.type.startsWith('touch');
                    let cx = isTouch ? (e.touches && e.touches[0] ? e.touches[0].clientX : 0) : e.clientX;
                    let cy = isTouch ? (e.touches && e.touches[0] ? e.touches[0].clientY : 0) : e.clientY;

                    // Hitung offset dari posisi visual elemen saat ini, bukan dari state tersimpan
                    let rect = winEl.getBoundingClientRect();
                    let offsetX = cx - rect.left;
                    let offsetY = cy - rect.top;

                    document.body.style.userSelect = 'none';
                    document.body.style.webkitUserSelect = 'none';
                    document.body.style.cursor = 'grabbing';

                    const onMove = (ev) => {
                        if (ev.cancelable) ev.preventDefault();
                        let curX = ev.type && ev.type.startsWith('touch') ? (ev.touches && ev.touches[0] ? ev.touches[0].clientX : 0) : ev.clientX;
                        let curY = ev.type && ev.type.startsWith('touch') ? (ev.touches && ev.touches[0] ? ev.touches[0].clientY : 0) : ev.clientY;

                        let maxX = Math.max(10, window.innerWidth - 60);
                        let maxY = Math.max(10, window.innerHeight - 60);
                        let newX = Math.max(0, Math.min(maxX, curX - offsetX));
                        let newY = Math.max(0, Math.min(maxY, curY - offsetY));

                        this.winPos[tool].x = newX;
                        this.winPos[tool].y = newY;
                        winEl.style.left = newX + 'px';
                        winEl.style.top = newY + 'px';
                    };

                    const onUp = () => {
                        window.removeEventListener('mousemove', onMove);
                        window.removeEventListener('mouseup', onUp);
                        window.removeEventListener('touchmove', onMove);
                        window.removeEventListener('touchend', onUp);
                        document.body.style.userSelect = '';
                        document.body.style.webkitUserSelect = '';
                        document.body.style.cursor = '';
                        try { localStorage.setItem('employee_exam_tools_pos', JSON.stringify(this.winPos)); } catch(err) {}
                    };

                    window.addEventListener('mousemove', onMove, { passive: false });
                    window.addEventListener('mouseup', onUp);
                    window.addEventListener('touchmove', onMove, { passive: false });
                    window.addEventListener('touchend', onUp);
                },
                // CATATAN
                notesText: '',
                confirmClearNotes: false,
                initNotes() {
                    this.notesText = localStorage.getItem('employee_exam_notes_' + this.testId) || '';
                },
                saveNotes() { 
                    try { localStorage.setItem('employee_exam_notes_' + this.testId, this.notesText); } catch(e) {}
                },
                clearNotes() {
                    if (!this.confirmClearNotes) {
                        this.confirmClearNotes = true;
                        setTimeout(() => { this.confirmClearNotes = false; }, 3500);
                        return;
                    }
                    this.notesText = '';
                    localStorage.removeItem('employee_exam_notes_' + this.testId);
                    this.confirmClearNotes = false;
                },
                insertQuestionNote() {
                    this.notesText += (this.notesText ? '\n' : '') + '[Catatan Soal]: ';
                    this.saveNotes();
                },
                // KALKULATOR
                calcExpr: '',
                calcDisplay: '0',
                calcEvaluated: false,
                calcTape: [],
                confirmClearCalcTape: false,
                initCalcTape() {
                    try {
                        this.calcTape = JSON.parse(localStorage.getItem('employee_exam_calc_tape_' + this.testId) || '[]');
                    } catch(e) { this.calcTape = []; }
                },
                clearCalcTape() {
                    if (!this.confirmClearCalcTape) {
                        this.confirmClearCalcTape = true;
                        setTimeout(() => { this.confirmClearCalcTape = false; }, 3500);
                        return;
                    }
                    this.calcTape = [];
                    this.calcExpr = '';
                    this.calcDisplay = '0';
                    this.calcEvaluated = false;
                    localStorage.removeItem('employee_exam_calc_tape_' + this.testId);
                    this.confirmClearCalcTape = false;
                },
                useCalcResult(line) {
                    if (!line) return;
                    let parts = line.split('=');
                    if (parts.length > 1) {
                        let res = parts[1].trim();
                        this.calcExpr = res;
                        this.calcDisplay = res;
                        this.calcEvaluated = true;
                    }
                },
                evalCalcExpr(expr) {
                    if (!expr) return null;
                    let clean = expr.replace(/×/g, '*').replace(/÷/g, '/').replace(/−/g, '-').replace(/,/g, '.');
                    clean = clean.trim();
                    while (/[\+\-\*\/]$/.test(clean)) {
                        clean = clean.slice(0, -1).trim();
                    }
                    if (!clean) return null;
                    if (!/^[\d+\-*/.\s()]+$/.test(clean)) return null;
                    clean = clean.replace(/\b0+(\d+)/g, '$1');
                    try {
                        let res = Function('"use strict"; return (' + clean + ')')();
                        if (typeof res === 'number' && !isNaN(res) && isFinite(res)) {
                            return parseFloat(res.toFixed(10));
                        }
                        return null;
                    } catch(e) {
                        return null;
                    }
                },
                pressCalc(k) {
                    // Tombol Clear (C)
                    if (k === 'C') {
                        this.calcExpr = '';
                        this.calcDisplay = '0';
                        this.calcEvaluated = false;
                        return;
                    }

                    // Tombol Backspace (⌫)
                    if (k === 'BS') {
                        if (this.calcEvaluated) {
                            this.calcEvaluated = false;
                        }
                        if (this.calcExpr.endsWith(' ')) {
                            this.calcExpr = this.calcExpr.trimEnd();
                            this.calcExpr = this.calcExpr.slice(0, -1).trimEnd();
                        } else if (this.calcExpr.length > 0) {
                            this.calcExpr = this.calcExpr.slice(0, -1);
                        }

                        if (!this.calcExpr) {
                            this.calcExpr = '';
                            this.calcDisplay = '0';
                        } else {
                            let val = this.evalCalcExpr(this.calcExpr);
                            this.calcDisplay = val !== null ? String(val).replace('.', ',') : '0';
                        }
                        return;
                    }

                    // Tombol Negatif / Positif (±)
                    if (k === 'NEG') {
                        if (this.calcEvaluated) {
                            let val = parseFloat(this.calcDisplay.replace(',', '.'));
                            if (!isNaN(val)) {
                                val = -val;
                                let str = String(val).replace('.', ',');
                                this.calcDisplay = str;
                                this.calcExpr = str;
                            }
                            return;
                        }
                        let match = this.calcExpr.match(/(-?\d+(?:,\d+)?)$/);
                        if (match) {
                            let numStr = match[1];
                            let toggled = numStr.startsWith('-') ? numStr.slice(1) : '-' + numStr;
                            this.calcExpr = this.calcExpr.slice(0, -numStr.length) + toggled;
                            let val = this.evalCalcExpr(this.calcExpr);
                            if (val !== null) this.calcDisplay = String(val).replace('.', ',');
                        } else if (!this.calcExpr || this.calcExpr === '0') {
                            this.calcExpr = '-';
                            this.calcDisplay = '-0';
                        }
                        return;
                    }

                    // Tombol Persen (%)
                    if (k === '%') {
                        if (this.calcEvaluated) {
                            let val = parseFloat(this.calcDisplay.replace(',', '.')) / 100;
                            val = parseFloat(val.toFixed(10));
                            let str = String(val).replace('.', ',');
                            this.calcDisplay = str;
                            this.calcExpr = str;
                            return;
                        }
                        let match = this.calcExpr.match(/(\d+(?:,\d+)?)$/);
                        if (match) {
                            let numStr = match[1];
                            let val = parseFloat(numStr.replace(',', '.')) / 100;
                            val = parseFloat(val.toFixed(10));
                            let valStr = String(val).replace('.', ',');
                            this.calcExpr = this.calcExpr.slice(0, -numStr.length) + valStr;
                            let res = this.evalCalcExpr(this.calcExpr);
                            if (res !== null) this.calcDisplay = String(res).replace('.', ',');
                        }
                        return;
                    }

                    // Tombol Operator (+, -, *, /)
                    if (['+', '-', '*', '/'].includes(k)) {
                        let symMap = { '+': '+', '-': '−', '*': '×', '/': '÷' };
                        let sym = symMap[k] || k;

                        if (this.calcEvaluated) {
                            this.calcExpr = this.calcDisplay + ' ' + sym + ' ';
                            this.calcEvaluated = false;
                            return;
                        }

                        if (!this.calcExpr) {
                            if (k === '-') {
                                this.calcExpr = '-';
                                return;
                            }
                            this.calcExpr = '0 ' + sym + ' ';
                            return;
                        }

                        // Jika sebelumnya sudah ada operator di akhir (misal "9 + "), ganti operatornya
                        if (/\s[+−×÷]\s$/.test(this.calcExpr)) {
                            this.calcExpr = this.calcExpr.replace(/\s[+−×÷]\s$/, ' ' + sym + ' ');
                        } else {
                            this.calcExpr += ' ' + sym + ' ';
                        }
                        return;
                    }

                    // Tombol Sama Dengan (=)
                    if (k === '=') {
                        if (!this.calcExpr) return;
                        let res = this.evalCalcExpr(this.calcExpr);
                        if (res !== null) {
                            let cleanExpr = this.calcExpr.replace(/\s[+−×÷]\s*$/, '').trim();
                            let resStr = String(res).replace('.', ',');
                            this.calcTape.unshift(`${cleanExpr} = ${resStr}`);
                            if (this.calcTape.length > 30) this.calcTape.pop();
                            try {
                                localStorage.setItem('employee_exam_calc_tape_' + this.testId, JSON.stringify(this.calcTape));
                            } catch(e) {}
                            this.calcDisplay = resStr;
                            this.calcExpr = cleanExpr;
                            this.calcEvaluated = true;
                        } else {
                            this.calcDisplay = 'Error';
                            this.calcEvaluated = true;
                        }
                        return;
                    }

                    // Tombol Koma (,) atau Titik (.)
                    if (k === ',' || k === '.') {
                        if (this.calcEvaluated) {
                            this.calcExpr = '0,';
                            this.calcDisplay = '0';
                            this.calcEvaluated = false;
                            return;
                        }
                        if (!this.calcExpr || this.calcExpr.endsWith(' ')) {
                            this.calcExpr += '0,';
                            return;
                        }
                        let lastToken = this.calcExpr.split(/\s+/).pop();
                        if (!lastToken.includes(',')) {
                            this.calcExpr += ',';
                        }
                        return;
                    }

                    // Tombol Digit (0 - 9)
                    if (/^\d$/.test(k)) {
                        if (this.calcEvaluated) {
                            this.calcExpr = k;
                            this.calcDisplay = k;
                            this.calcEvaluated = false;
                            return;
                        }
                        if (this.calcExpr === '0') {
                            this.calcExpr = k;
                        } else {
                            this.calcExpr += k;
                        }
                        let val = this.evalCalcExpr(this.calcExpr);
                        if (val !== null) {
                            this.calcDisplay = String(val).replace('.', ',');
                        }
                    }
                },
                // EXCEL SPREADSHEET TOOL
                excelRows: 20,
                excelCols: ['A','B','C','D','E','F','G'],
                excelCells: {},
                selectedCell: 'A1',
                editingCell: null,
                activeFormula: '',
                confirmClearExcel: false,
                initExcel() {
                    try {
                        this.excelCells = JSON.parse(localStorage.getItem('employee_exam_excel_' + this.testId) || '{}');
                    } catch(e) { this.excelCells = {}; }
                },
                selectExcelCell(cell) {
                    this.selectedCell = cell;
                    this.activeFormula = this.excelCells[cell] || '';
                },
                updateFromFormulaBar() {
                    this.setCell(this.selectedCell, this.activeFormula);
                },
                setCell(cell, val) {
                    if (!val || val.trim() === '') {
                        delete this.excelCells[cell];
                    } else {
                        this.excelCells[cell] = val;
                    }
                    try { localStorage.setItem('employee_exam_excel_' + this.testId, JSON.stringify(this.excelCells)); } catch(e) {}
                },
                addExcelRow() {
                    this.excelRows += 5;
                },
                clearExcel() {
                    if (!this.confirmClearExcel) {
                        this.confirmClearExcel = true;
                        setTimeout(() => { this.confirmClearExcel = false; }, 3500);
                        return;
                    }
                    this.excelCells = {};
                    this.activeFormula = '';
                    localStorage.removeItem('employee_exam_excel_' + this.testId);
                    this.confirmClearExcel = false;
                },
                exportExcelCSV() {
                    let csv = '';
                    csv += ['#', ...this.excelCols].join(',') + '\r\n';
                    for (let r = 1; r <= this.excelRows; r++) {
                        let rowVals = [r];
                        let q = '"';
                        for (let c of this.excelCols) {
                            let raw = this.excelCells[`${c}${r}`] || '';
                            let val = this.evalCell(raw);
                            rowVals.push(q + String(val).split(q).join(q + q) + q);
                        }
                        csv += rowVals.join(',') + '\r\n';
                    }
                    let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                    let url = URL.createObjectURL(blob);
                    let link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', 'lembar_kerja_fat_' + this.testId + '.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                evalCell(val) {
                    if (!val) return '';
                    if (typeof val !== 'string' || !val.startsWith('=')) return val;
                    try {
                        let expr = val.slice(1).trim().toUpperCase();
                        // Handle SUM(A1:A5)
                        expr = expr.replace(/SUM\(([A-G])(\d+):([A-G])(\d+)\)/g, (m, c1, r1, c2, r2) => {
                            let startR = Math.min(parseInt(r1), parseInt(r2));
                            let endR = Math.max(parseInt(r1), parseInt(r2));
                            let cStart = c1.charCodeAt(0);
                            let cEnd = c2.charCodeAt(0);
                            let vals = [];
                            for (let c = Math.min(cStart, cEnd); c <= Math.max(cStart, cEnd); c++) {
                                let colLetter = String.fromCharCode(c);
                                for (let r = startR; r <= endR; r++) {
                                    let cVal = parseFloat(this.evalCell(this.excelCells[`${colLetter}${r}`] || '0')) || 0;
                                    vals.push(cVal);
                                }
                            }
                            return '(' + (vals.length ? vals.join('+') : '0') + ')';
                        });
                        // Handle AVERAGE(A1:A5)
                        expr = expr.replace(/AVERAGE\(([A-G])(\d+):([A-G])(\d+)\)/g, (m, c1, r1, c2, r2) => {
                            let startR = Math.min(parseInt(r1), parseInt(r2));
                            let endR = Math.max(parseInt(r1), parseInt(r2));
                            let cLetter = c1;
                            let vals = [];
                            for (let r = startR; r <= endR; r++) {
                                let cVal = parseFloat(this.evalCell(this.excelCells[`${cLetter}${r}`] || '0')) || 0;
                                vals.push(cVal);
                            }
                            let cnt = vals.length || 1;
                            return '((' + (vals.join('+') || '0') + ')/' + cnt + ')';
                        });
                        // Replace cell references A1, B2 etc
                        expr = expr.replace(/\b([A-G])(\d+)\b/g, (m, c, r) => {
                            let cellContent = this.excelCells[`${c}${r}`] || '0';
                            let parsed = parseFloat(this.evalCell(cellContent));
                            return isNaN(parsed) ? '0' : String(parsed);
                        });
                        // Safe sanitized math evaluation
                        if (!/^[0-9+\-*/().\s]+$/.test(expr)) return '#VALUE!';
                        let res = Function('return (' + expr + ')')();
                        if (res === Infinity || res === -Infinity || isNaN(res)) return '#DIV/0!';
                        return Number.isInteger(res) ? String(res) : parseFloat(res.toFixed(4)).toString();
                    } catch(e) {
                        return '#ERROR!';
                    }
                },
                violations: 0, // diisi dari data-violations saat init
                maxViolations: 3,
                isGuarded: false,
                violationReason: '',
                isGracePeriod: true,
                lastViolationTime: 0,
                _graceTimer: null,
                isWindowFocused: true,
                isScreenObfuscated: false,
                isFileDialogActive: false,
                _focusLostAt: 0,
                watermarkClock: '',
                updateWatermarkClock() {
                    let d = new Date();
                    let pad = (n) => String(n).padStart(2, '0');
                    let dateStr = pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
                    let timeStr = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
                    this.watermarkClock = dateStr + ' ' + timeStr + ' WIB';
                },
                wipeClipboard() {
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText('').catch(() => {});
                        }
                    } catch(err) {}
                },
                showSecurityToast(msg) {
                    if (typeof showCopyBlockedToast === 'function') {
                        showCopyBlockedToast(msg);
                    }
                },
                returnToFullscreen() {
                    if (this.violations >= this.maxViolations || this.isAutoSubmitting) return;
                    const clear = () => {
                        this.isGuarded = false;
                        this.isScreenObfuscated = false;
                        this.startGracePeriod(2);
                    };
                    // Soal uraian: tidak ada kewajiban fullscreen, cukup tutup overlay
                    if (this.checkFullscreen() || this.isEssayQuestion()) return clear();
                    let res = this.requestFullscreen();
                    if (res && res.then) res.then(clear).catch(() => {});
                    else if (this.checkFullscreen()) clear();
                },
                startGracePeriod(seconds = 2) {
                    this.isGracePeriod = true;
                    if (this._graceTimer) clearTimeout(this._graceTimer);
                    this._graceTimer = setTimeout(() => {
                        this.isGracePeriod = false;
                    }, seconds * 1000);
                },
                checkFullscreen() {
                    return !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
                },
                openFileDialog() {
                    this.isFileDialogActive = true;
                },
                currentQuestionType() {
                    const el = document.getElementById('employee-exam-root');
                    return el ? (el.dataset.currentQuestionType || '') : '';
                },
                isEssayQuestion() {
                    return this.testState === 'taking' && this.currentQuestionType() === 'essay';
                },
                // Soal uraian: boleh minimize / pindah tab, tetapi screenshot tetap dilarang.
                // Soal lain: fullscreen wajib seperti biasa.
                syncProctoringRequirement() {
                    if (this.isEssayQuestion()) {
                        try {
                            if (navigator.keyboard && navigator.keyboard.unlock) navigator.keyboard.unlock();
                        } catch (err) {}
                        return;
                    }
                    if (this.testState !== 'taking' || this.checkFullscreen()) return;
                    const res = this.requestFullscreen();
                    if (res && res.then) {
                        res.then(() => { this.isGuarded = false; }).catch(() => {
                            if (!this.isGuarded) this.isGuarded = true;
                        });
                    }
                },
                enforceFullscreen(reason) {
                    if (this.testState !== 'taking' || this.isGracePeriod || this.isFileDialogActive) return;
                    if (this.isEssayQuestion()) return;
                    this.triggerViolation(reason || 'Anda keluar dari mode layar penuh. Klik tombol di bawah untuk melanjutkan asesmen.');
                },
                initProctoring() {
                    if (window._employeeProctoringCleanup) {
                        try { window._employeeProctoringCleanup(); } catch (err) {}
                    }

                    const onFullscreenChange = () => {
                        if (this.testState !== 'taking' || this.checkFullscreen()) return;
                        if (this.isEssayQuestion()) return;
                        // Dialog native & pemilih file ikut melepas fullscreen.
                        // Kasus itu selalu didahului blur, jadi hanya peringatan tanpa strike.
                        const lostFocusRecently = Date.now() - this._focusLostAt < 2000;
                        if (document.hasFocus() && !this.isFileDialogActive && !lostFocusRecently) {
                            this.enforceFullscreen('Anda keluar dari mode layar penuh. Klik tombol di bawah untuk melanjutkan asesmen.');
                        } else if (!this.isGuarded) {
                            this.isGuarded = true;
                            if (!this.violationReason) {
                                this.violationReason = 'Klik tombol di bawah untuk masuk kembali ke mode layar penuh.';
                            }
                        }
                    };
                    const onScreenshotKey = (e) => {
                        if (e.key !== 'PrintScreen' && e.code !== 'PrintScreen') return;
                        // Clipboard OS baru terisi setelah keyup, jadi bersihkan dua kali
                        this.wipeClipboard();
                        setTimeout(() => this.wipeClipboard(), 150);
                        this.triggerViolation('Aktivitas tangkapan layar (PrintScreen / Snipping Tool) terdeteksi! Dilarang menyalin atau mendokumentasikan soal asesmen.', { screenshot: true });
                    };
                    const onKeyDown = (e) => {
                        if (e.key === 'Escape' && this.testState === 'taking') {
                            if (this.isEssayQuestion()) return;
                            e.preventDefault();
                            if (!this.checkFullscreen()) this.enforceFullscreen('Tombol ESC ditekan atau mode layar penuh dibatalkan.');
                            return;
                        }
                        if (this.testState !== 'taking') return;
                        const mod = e.ctrlKey || e.metaKey;
                        const key = String(e.key).toLowerCase();
                        const blocked = e.key === 'F12'
                            || (mod && e.shiftKey && ['i', 'j', 'c'].includes(key))
                            || (mod && ['u', 'p'].includes(key));
                        if (blocked) {
                            e.preventDefault();
                            e.stopPropagation();
                            this.showSecurityToast('Shortcut ini diblokir selama asesmen berlangsung.');
                        }
                    };
                    const onBlur = () => {
                        if (this.testState !== 'taking' || this.isFileDialogActive) return;
                        this._focusLostAt = Date.now();
                        // Delay: dialog native membaca ulang fokus setelah ditutup
                        setTimeout(() => {
                            if (this.testState === 'taking' && !this.isFileDialogActive && !document.hasFocus()) {
                                this.enforceFullscreen('Jendela asesmen kehilangan fokus (berpindah tab / aplikasi terdeteksi).');
                            }
                        }, 300);
                    };
                    const onVisibility = () => { if (document.hidden) onBlur(); };
                    const onFocus = () => { this.isFileDialogActive = false; };

                    // Tipe soal berubah (navigasi soal) -> sesuaikan kebutuhan fullscreen
                    const rootEl = document.getElementById('employee-exam-root');
                    const typeObserver = (rootEl && window.MutationObserver)
                        ? new MutationObserver(() => this.syncProctoringRequirement())
                        : null;
                    if (typeObserver) {
                        typeObserver.observe(rootEl, { attributes: true, attributeFilter: ['data-current-question-type'] });
                    }

                    document.addEventListener('fullscreenchange', onFullscreenChange);
                    document.addEventListener('webkitfullscreenchange', onFullscreenChange);
                    document.addEventListener('keydown', onKeyDown, true);
                    document.addEventListener('keyup', onScreenshotKey, true);
                    document.addEventListener('visibilitychange', onVisibility);
                    window.addEventListener('blur', onBlur);
                    window.addEventListener('focus', onFocus);

                    window._employeeProctoringCleanup = () => {
                        if (typeObserver) typeObserver.disconnect();
                        document.removeEventListener('fullscreenchange', onFullscreenChange);
                        document.removeEventListener('webkitfullscreenchange', onFullscreenChange);
                        document.removeEventListener('keydown', onKeyDown, true);
                        document.removeEventListener('keyup', onScreenshotKey, true);
                        document.removeEventListener('visibilitychange', onVisibility);
                        window.removeEventListener('blur', onBlur);
                        window.removeEventListener('focus', onFocus);
                        window._employeeProctoringCleanup = null;
                    };
                },
                submitAutoExam() {
                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                        this.timerInterval = null;
                    }
                    this.isAutoSubmitting = true;
                    if (this.$wire) {
                        this.$wire.finishTestAuto(this.violations)
                            .then(() => {
                                this.testState = 'completed';
                                this.isGuarded = false;
                                this.isScreenObfuscated = false;
                                this.isAutoSubmitting = false;
                                if (document.fullscreenElement && document.exitFullscreen) {
                                    document.exitFullscreen().catch(() => {});
                                }
                            })
                            .catch((err) => {
                                console.error('finishTestAuto error:', err);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            });
                    }
                },
                triggerViolation(reason, opts = {}) {
                    if (this.testState !== 'taking' || this.isAutoSubmitting) return;
                    if (this.isGracePeriod || this.isFileDialogActive) return;
                    if (this.violations >= this.maxViolations) return;
                    const now = Date.now();
                    if (now - this.lastViolationTime < 3000) return; // cooldown agar tidak kena 3 strike sekaligus
                    this.lastViolationTime = now;

                    this.violations++;
                    this.violationReason = reason || 'Keluar dari mode layar penuh / berpindah jendela';
                    this.isScreenObfuscated = opts.screenshot === true;
                    this.isGuarded = !this.isScreenObfuscated;
                    this.wipeClipboard();
                    this.showSecurityToast(this.violationReason);

                    if (this.$wire) this.$wire.recordViolation(this.violationReason, this.violations).catch(() => {});
                },
                requestFullscreen() {
                    this.startGracePeriod(2);
                    const el = document.documentElement;
                    const req = el.requestFullscreen || el.webkitRequestFullscreen || el.mozRequestFullScreen || el.msRequestFullscreen;
                    if (!req) return null;
                    const lockEsc = () => {
                        if (this.isEssayQuestion()) return;
                        try {
                            if (navigator.keyboard && navigator.keyboard.lock) navigator.keyboard.lock(['Escape']).catch(() => {});
                        } catch (err) {}
                    };
                    try {
                        const res = req.call(el);
                        if (res && res.then) res.then(lockEsc).catch(() => {}); else lockEsc();
                        return res;
                    } catch (err) {
                        return null;
                    }
                },
                formatTime(sec) {
                    if (sec <= 0) return '00:00:00';
                    let h = Math.floor(sec / 3600);
                    let m = Math.floor((sec % 3600) / 60);
                    let s = sec % 60;
                    return (h > 0 ? String(h).padStart(2, '0') + ':' : '') + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },
                startTimer() {
                    if (this.timeRemaining > 0 && !this.timerInterval) {
                        this.updateWatermarkClock();
                        this.timerInterval = setInterval(() => {
                            this.updateWatermarkClock();
                            if (this.timeRemaining > 0) {
                                this.timeRemaining--;
                            } else {
                                clearInterval(this.timerInterval);
                                this.timerInterval = null;
                                this.submitAutoExam();
                            }
                        }, 1000);
                    }
                },
                init() {
                    this.initNotes();
                    this.initCalcTape();
                    this.initExcel();
                    this.updateWatermarkClock();

                    // Nilai dari server dibaca sekali di sini (bukan lewat x-data) agar re-render Livewire tidak me-reset state Alpine
                    this.timeRemaining = parseInt(this.$el.dataset.timeRemaining || '0', 10) || 0;
                    this.violations = parseInt(this.$el.dataset.violations || '0', 10) || 0;

                    // Beri tenggang waktu aman saat komponen dimuat
                    this.startGracePeriod(2);

                    this.initProctoring();

                    // Sesi aktif yang dibuka ulang di luar fullscreen: tampilkan guard tanpa menambah strike
                    setTimeout(() => {
                        if (this.testState === 'taking' && !this.checkFullscreen() && !this.isGuarded && !this.isEssayQuestion()) {
                            this.isGuarded = true;
                            if (!this.violationReason) {
                                this.violationReason = 'Sesi asesmen Anda sedang berjalan. Silakan klik tombol di bawah untuk masuk ke mode layar penuh (Fullscreen).';
                            }
                        }
                    }, 300);

                    if (this.testState === 'taking') {
                        this.startTimer();
                        if (typeof initCopyProtection === 'function') initCopyProtection();
                    }

                    if (this.$wire) {
                        this.$wire.on('test-started', (e) => {
                            let sec = (Array.isArray(e) && e[0]) ? (e[0].remaining || e[0]) : (e?.remaining || e);
                            this.timeRemaining = parseInt(sec) || 10800;
                            this.testState = 'taking';
                            this.updateWatermarkClock();
                            this.startTimer();
                            this.startGracePeriod(2);
                            if (typeof initCopyProtection === 'function') initCopyProtection();
                        });
                        this.$wire.on('test-completed', () => {
                            this.testState = 'completed';
                            this.isGuarded = false;
                            this.isScreenObfuscated = false;
                            this.isAutoSubmitting = false;
                            if (this.timerInterval) {
                                clearInterval(this.timerInterval);
                                this.timerInterval = null;
                            }
                            if (document.fullscreenElement && document.exitFullscreen) {
                                document.exitFullscreen().catch(() => {});
                            }
                        });
                        this.$wire.on('open-confirm-modal', () => { this.showConfirmModal = true; });
                        this.$wire.on('close-confirm-modal', () => { this.showConfirmModal = false; });
                    }

                    // Deteksi fullscreen, ESC, screenshot, blur, dan pindah tab dipasang di initProctoring()

                    window.addEventListener('keydown', (e) => { 
                        if (e.key === 'F11') {
                            e.preventDefault();
                        } 

                        // Cek apakah kursor sedang fokus pada elemen input teks (misal: cell Excel, textarea Catatan, atau Essay)
                        const activeTag = document.activeElement ? document.activeElement.tagName : '';
                        const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeTag) || document.activeElement?.isContentEditable;

                        // DUKUNGAN INPUT KEYBOARD & NUMPAD UNTUK KALKULATOR UJIAN
                        if (this.showCalc && !isTyping) {
                            let k = e.key;
                            // Angka 0 - 9
                            if (/^[0-9]$/.test(k)) {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc(k);
                                return false;
                            }
                            // Operator Aritmatika (+, -, *, /)
                            if (['+', '-', '*', '/'].includes(k)) {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc(k);
                                return false;
                            }
                            // Alternatif tombol kali: x atau X
                            if (k === 'x' || k === 'X') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc('*');
                                return false;
                            }
                            // Sama dengan (=) atau Enter
                            if (k === '=' || k === 'Enter') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc('=');
                                return false;
                            }
                            // Titik atau koma desimal
                            if (k === '.' || k === ',') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc(',');
                                return false;
                            }
                            // Persen (%)
                            if (k === '%') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc('%');
                                return false;
                            }
                            // Backspace (hapus satu digit)
                            if (k === 'Backspace') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc('BS');
                                return false;
                            }
                            // Clear C / Delete
                            if (k.toLowerCase() === 'c' || k === 'Delete') {
                                e.preventDefault();
                                e.stopPropagation();
                                this.pressCalc('C');
                                return false;
                            }
                        }
                    });
                }
            };
        };
    }
    </script>

    <!-- Header Breadcrumb & Title -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-1">
                <a href="{{ route('profile') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard
                    Asesmen</a>
                <span>/</span>
                <span
                    class="text-gray-900 dark:text-white font-medium">{{ $test->department->name ?? 'Seluruh Divisi' }}</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $test->title }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                {{ $test->title }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Kategori: <span
                    class="font-bold text-indigo-700 dark:text-indigo-300">{{ $test->category->name ?? 'Asesmen Karyawan' }}</span>
                &bull;
                Divisi: <span
                    class="font-semibold text-gray-700 dark:text-gray-300">{{ $test->department->name ?? 'Umum / Semua Divisi' }}</span>
            </p>
        </div>

        @if ($testState === 'taking')
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 shrink-0">
                <!-- SHORTCUT TOOLS BAR (DI ATAS SEBELAH WAKTU) -->
                <div class="flex items-center gap-1 sm:gap-1.5 bg-white dark:bg-[#0D1527] p-1.5 rounded-xl border border-slate-200 dark:border-[#1D2E54] shadow-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 hidden lg:inline">Alat Bantu:</span>

                    <!-- Tombol Excel -->
                    <button type="button" @click="toggleTool('excel')"
                        class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                        :class="showExcel ? 'bg-emerald-500 text-slate-950 font-black shadow-xs' : 'bg-slate-50 hover:bg-slate-100 dark:bg-[#14203A] dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 border border-slate-200/80 dark:border-emerald-500/20'">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Excel</span>
                    </button>

                    <!-- Tombol Kalkulator -->
                    <button type="button" @click="toggleTool('calc')"
                        class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                        :class="showCalc ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'bg-slate-50 hover:bg-slate-100 dark:bg-[#14203A] dark:hover:bg-slate-700 text-amber-600 dark:text-amber-400 border border-slate-200/80 dark:border-amber-400/20'">
                        <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span>Kalkulator</span>
                    </button>

                    <!-- Tombol Catatan -->
                    <button type="button" @click="toggleTool('notes')"
                        class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                        :class="showNotes ? 'bg-blue-600 text-white font-black shadow-xs' : 'bg-slate-50 hover:bg-slate-100 dark:bg-[#14203A] dark:hover:bg-slate-700 text-blue-600 dark:text-blue-400 border border-slate-200/80 dark:border-blue-400/20'">
                        <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Catatan</span>
                    </button>
                </div>

                <!-- Countdown Timer Badge -->
                <div
                    class="flex items-center gap-3 bg-white dark:bg-gray-800 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs shrink-0">
                    <div
                        class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span
                            class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sisa
                            Waktu</span>
                        <span class="text-base font-bold text-rose-600 dark:text-rose-400 font-mono"
                            x-text="formatTime(timeRemaining)">--:--</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Flash Messages (Errors / Warnings / Success) -->
    @if (session()->has('test_error') || session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/80 flex items-start gap-3 text-red-800 dark:text-red-300">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1 text-xs">
                <p class="font-semibold text-red-900 dark:text-red-200 mb-0.5">Ujian Belum Dapat Dikirim</p>
                <p class="text-red-700 dark:text-red-300 leading-relaxed font-normal">
                    {{ session('test_error') ?: session('error') }}
                </p>
            </div>
            <button type="button" @click="show = false"
                class="text-red-400 hover:text-red-600 dark:hover:text-red-200 transition p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
            class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between gap-3 text-xs text-emerald-800 dark:text-emerald-200">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- STATE 1: INTRO / PETUNJUK PENGERJAAN -->
    @if ($testState === 'intro')
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-4 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span
                        class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        {{ $test->category->name ?? 'Kategori Asesmen' }}
                    </span>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mt-1">Petunjuk Pelaksanaan Asesmen
                        Karyawan</h2>
                </div>
            </div>

            <!-- SELEKSI PAKET UJIAN & KARTU UJIAN (CBT STYLE) -->
            @if ($this->isFatTest())
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                        <!-- Sisi Kiri: Paket Ujian Cards (5 cols) -->
                        <div class="lg:col-span-5 flex flex-col justify-between space-y-3">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">
                                Paket Ujian
                            </span>
                            <div class="grid grid-cols-2 gap-3 flex-1">
                                <template x-for="pkg in packages" :key="pkg.id">
                                    <button type="button" @click="selectedPkg = pkg.id; $wire.set('selectedPkg', pkg.id)"
                                        class="p-4 rounded-2xl text-left border-2 transition-all duration-200 flex flex-col justify-between cursor-pointer"
                                        :class="selectedPkg === pkg.id 
                                            ? 'bg-gray-900 text-white border-amber-400 ring-2 ring-amber-400/30 shadow-lg' 
                                            : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                                        <div>
                                            <span class="block text-[11px] font-extrabold uppercase tracking-wider" 
                                                  :class="selectedPkg === pkg.id ? 'text-amber-400' : 'text-gray-400'" 
                                                  x-text="pkg.name"></span>
                                            <div class="text-xl sm:text-2xl font-black mt-1">
                                                <span x-text="pkg.questions"></span> <span class="text-xs font-semibold" :class="selectedPkg === pkg.id ? 'text-gray-300' : 'text-gray-400'">soal</span>
                                            </div>
                                        </div>
                                        <div class="text-xs font-mono mt-3 font-semibold" :class="selectedPkg === pkg.id ? 'text-amber-300' : 'text-gray-500 dark:text-gray-400'">
                                            <span x-text="pkg.duration"></span> menit
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Sisi Kanan: Kartu Ujian & Alat Bantu (7 cols) -->
                        <div class="lg:col-span-7 rounded-2xl bg-[#091124] border border-gray-800 p-5 sm:p-6 text-white space-y-5 shadow-xl flex flex-col justify-between">
                            <!-- Header Kartu Ujian -->
                            <div>
                                <div class="flex items-center justify-between pb-3 border-b border-gray-800">
                                    <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                        </svg>
                                        <span>Kartu Ujian</span>
                                    </h3>
                                    <span class="text-xs font-black tracking-widest text-amber-400 font-mono" x-text="packages.find(p => p.id === selectedPkg)?.name">PAKET 1</span>
                                </div>

                                <!-- 3 Kolom Indikator Soal, Waktu, Lulus -->
                                <div class="grid grid-cols-3 gap-4 pt-4 text-center divide-x divide-gray-800">
                                    <div>
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Soal</span>
                                        <div class="text-2xl sm:text-3xl font-black text-white mt-0.5" x-text="packages.find(p => p.id === selectedPkg)?.questions">100</div>
                                        <span class="text-[11px] text-gray-400">pilihan ganda</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Waktu</span>
                                        <div class="text-2xl sm:text-3xl font-black text-white mt-0.5" x-text="packages.find(p => p.id === selectedPkg)?.duration">240</div>
                                        <span class="text-[11px] text-gray-400">menit</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Lulus</span>
                                        <div class="text-2xl sm:text-3xl font-black text-emerald-400 mt-0.5" x-text="'≥' + (packages.find(p => p.id === selectedPkg)?.passPct || 61) + '%'">≥61%</div>
                                        <span class="text-[11px] text-gray-400" x-text="'min. ' + (packages.find(p => p.id === selectedPkg)?.minCorrect || 61) + ' benar'">min. 61 benar</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Alat Bantu Saat Ujian Box -->
                            <div class="rounded-xl bg-[#0F1C38] border border-gray-700/60 p-4 space-y-2.5">
                                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-gray-400">
                                    Alat Bantu Saat Ujian
                                </span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-800/90 border border-gray-700 text-xs font-semibold text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        Excel
                                    </span>
                                    {{-- Tombol Word disembunyikan karena alat Word tidak tersedia (sama seperti modul pelamar).
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-800/90 border border-gray-700 text-xs font-semibold text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Word
                                    </span> --}}
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-800/90 border border-gray-700 text-xs font-semibold text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        Catatan
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-800/90 border border-gray-700 text-xs font-semibold text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        Kalkulator
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-400 leading-tight">
                                    Bisa dibuka kapan saja selama ujian. Isinya tidak dinilai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Ringkasan Informasi Ujian Standar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Durasi Pengerjaan</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $test->duration_minutes }} Menit</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jumlah Butir Soal</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>{{ $test->total_questions > 0 ? $test->total_questions : count($questions) }} Soal</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Standar Kelulusan (KKM)</span>
                        <div class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $test->passing_score > 0 ? number_format($test->passing_score, 0) . '%' : 'Evaluasi' }}</span>
                        </div>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                        <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Alat Bantu Tersedia</span>
                        <div class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Kalkulator</span>
                            <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">Catatan</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulir Konfirmasi Biodata Peserta -->
            <div
                class="p-5 rounded-2xl bg-gradient-to-br from-indigo-50/70 via-blue-50/40 to-purple-50/50 dark:from-indigo-950/40 dark:via-gray-900/60 dark:to-purple-950/30 border border-indigo-100 dark:border-indigo-800/60 shadow-xs space-y-4">
                <div
                    class="flex items-center justify-between pb-3 border-b border-indigo-100 dark:border-indigo-800/60">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Konfirmasi Biodata Peserta Tes
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Data berikut akan dicantumkan secara resmi pada lembar hasil laporan & evaluasi PDF asesmen
                            Anda.
                        </p>
                    </div>
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Wajib Diisi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="participantName"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="participantName" wire:model.defer="participantName"
                            placeholder="Masukkan nama lengkap..."
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('participantName')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Usia -->
                    <div>
                        <label for="participantAge"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Usia (Tahun) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="participantAge" wire:model.defer="participantAge" min="15"
                            max="99" placeholder="Contoh: 24"
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('participantAge')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="participantGender"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Jenis Kelamin <span class="text-rose-500">*</span>
                        </label>
                        <select id="participantGender" wire:model.defer="participantGender"
                            class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                            <option value="male">Laki-laki / Pria</option>
                            <option value="female">Perempuan / Wanita</option>
                        </select>
                        @error('participantGender')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Pelaksanaan Tes -->
                    <div>
                        <label for="testDate"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Tanggal Tes <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="testDate" wire:model.defer="testDate"
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-900 dark:text-white outline-none transition shadow-2xs">
                        @error('testDate')
                            <p class="mt-1 text-[11px] text-rose-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Ketentuan Ujian -->
            <div
                class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-800 text-xs text-gray-700 dark:text-gray-300 space-y-2">
                <h3 class="font-semibold text-xs text-gray-900 dark:text-white">Peraturan & Hal yang Perlu
                    Diperhatikan:
                </h3>
                <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400 leading-relaxed">
                    <li>Pastikan biodata di atas sudah sesuai sebelum menekan tombol <strong>Mulai Ujian</strong>.</li>
                    <li>Pastikan koneksi internet Anda stabil sebelum pengerjaan dimulai.</li>
                    <li>Waktu akan langsung berjalan otomatis dan tidak dapat dijeda (pause).</li>
                    <li>Jawaban Anda otomatis tersimpan setiap kali Anda memilih opsi atau mengetik jawaban.</li>
                    <li>Jika butir soal memiliki <strong>file lampiran / studi kasus</strong>, tautan unduhan akan
                        muncul di atas pertanyaan.</li>
                    <li>Ujian akan otomatis disubmit jika batas waktu pengerjaan telah habis.</li>
                    <li>Selama asesmen, layar wajib berada dalam <strong>mode Fullscreen</strong>. Menekan ESC, pindah
tab/jendela, atau mengambil tangkapan layar (PrintScreen/Snipping Tool) dihitung sebagai pelanggaran
(3 pelanggaran = asesmen berhenti otomatis).</li>
                    <li><strong>Khusus soal uraian</strong>, Anda boleh meminimize jendela atau berpindah tab, tetapi
<strong>tangkapan layar tetap dilarang</strong>.</li>
                </ul>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    &larr; Kembali ke Dashboard
                </a>
                <button type="button" @click="startGracePeriod(2); requestFullscreen()" wire:click="startTest(selectedPkg)" wire:loading.attr="disabled" wire:target="startTest"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-xs font-medium rounded-lg transition shadow-xs cursor-pointer">
                    <svg wire:loading wire:target="startTest" class="animate-spin w-4 h-4 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span wire:loading.remove wire:target="startTest">Mulai Ujian</span>
                    <span wire:loading wire:target="startTest">Menyimpan & Memulai...</span>
                    <svg wire:loading.remove wire:target="startTest" class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- STATE 2: SEDANG MENGERJAKAN UJIAN (TAKING TEST) -->
    @if ($testState === 'taking')
        @php
            $isPapiPage  = collect($pageQuestions)->contains('question_type', 'papi_kostick');
            $isDiscPage  = collect($pageQuestions)->contains('question_type', 'disc');
            $isMultiPage = !$isPapiPage && !$isDiscPage;
        @endphp

        <!-- WATERMARK PENGAMAN ANTI-FOTO KAMERA HP & ANTI-BOCOR SOAL -->
        <div class="pointer-events-none select-none fixed inset-0 z-10 overflow-hidden opacity-[0.045] dark:opacity-[0.035] flex flex-wrap content-start gap-x-20 gap-y-16 p-6 -rotate-12">
            @for ($w = 0; $w < 24; $w++)
                <div class="font-mono text-xs font-black tracking-widest text-slate-900 dark:text-white uppercase whitespace-nowrap">
                    {{ $participantName ?: (auth()->user()->name ?? 'KARYAWAN CBT') }} &bull; {{ auth()->user()->email ?? '' }} &bull; <span x-text="watermarkClock"></span>
                </div>
            @endfor
        </div>

        <div x-init="testState = 'taking'; timeRemaining = {{ $timeRemainingSeconds }}; startTimer();" 
             class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start relative transition-all duration-200">
            <!-- Left Side: Lembar Soal & Jawaban (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">

                {{-- ============================================================ --}}
                {{-- LAYOUT TABEL: PAPI KOSTICK (semua soal halaman dalam 1 tabel) --}}
                {{-- ============================================================ --}}
                @if ($isPapiPage)
                    <div class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-[#1D2E54] shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-[#1D2E54] bg-indigo-900 text-white">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-0.5 rounded-lg bg-white/20 text-white font-black text-xs">
                                    Soal {{ $pageStart + 1 }}–{{ min($pageStart + $questionsPerPage, $totalQuestions) }}
                                </span>
                                <span class="text-white/80 text-xs font-medium">dari {{ $totalQuestions }} Soal PAPI Kostick</span>
                            </div>
                            <span class="text-[11px] font-semibold text-white/90">Pilih 1 pernyataan yang paling sesuai diri Anda (A atau B)</span>
                        </div>

                        <!-- Tabel semua soal di halaman ini -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse">
                                <thead>
                                    <tr class="border-b-2 border-slate-300 dark:border-[#1D2E54] text-center text-[11px] font-extrabold uppercase tracking-wider">
                                        <th class="w-12 py-2.5 px-2 bg-slate-200 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border-r-2 border-slate-300 dark:border-[#1D2E54]">No</th>
                                        <th class="w-14 py-2.5 px-2 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-800 dark:text-indigo-300 border-r-2 border-slate-300 dark:border-[#1D2E54]">Pilih</th>
                                        <th class="py-2.5 px-4 bg-slate-50 dark:bg-[#14203A]/50 text-slate-700 dark:text-slate-300 text-left">Pernyataan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pageQuestions as $pgIdx => $pq)
                                        @php
                                            $globalIdx    = $pageStart + $pgIdx;
                                            $selectedPapi = $answers[$pq['id']] ?? null;
                                            $isEven       = $pgIdx % 2 === 0;
                                        @endphp
                                        <tbody wire:key="papi-group-{{ $pq['id'] }}" wire:ignore
                                            x-data="{ sel: {{ $selectedPapi ?? 'null' }} }"
                                            class="border-t-4 {{ $isEven ? 'border-indigo-300 dark:border-indigo-700' : 'border-slate-300 dark:border-slate-600' }}">

                                            @foreach ($pq['options'] as $oIdx => $opt)
                                                @php $label = ['A','B','C','D','E'][$oIdx] ?? ($oIdx+1); @endphp
                                                <tr class="cursor-pointer"
                                                    :class="sel == {{ $opt['id'] }}
                                                        ? 'bg-indigo-50 dark:bg-indigo-950/40'
                                                        : '{{ $isEven ? 'hover:bg-slate-50 dark:hover:bg-[#14203A]/50' : 'hover:bg-slate-100/60 dark:hover:bg-[#14203A]/30' }}'"
                                                    @click="sel = {{ $opt['id'] }}; $wire.saveAnswer({{ $pq['id'] }}, {{ $opt['id'] }})">

                                                    @if ($oIdx === 0)
                                                        <td rowspan="{{ count($pq['options']) }}"
                                                            class="text-center font-black text-sm w-12 border-r-2 border-slate-300 dark:border-[#1D2E54] select-none align-middle
                                                                {{ $isEven ? 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-900 dark:text-indigo-100' : 'bg-slate-200 dark:bg-[#14203A] text-slate-800 dark:text-white' }}">
                                                            {{ $globalIdx + 1 }}
                                                        </td>
                                                    @endif

                                                    <td class="text-center w-14 border-r-2 border-slate-200 dark:border-[#1D2E54] py-0"
                                                        @click.stop="sel = {{ $opt['id'] }}; $wire.saveAnswer({{ $pq['id'] }}, {{ $opt['id'] }})">
                                                        <label class="flex items-center justify-center w-full h-11 cursor-pointer">
                                                            <input type="radio"
                                                                id="papi_opt_{{ $opt['id'] }}"
                                                                name="papi_q_{{ $pq['id'] }}"
                                                                value="{{ $opt['id'] }}"
                                                                :checked="sel == {{ $opt['id'] }}"
                                                                class="w-4 h-4 text-indigo-600 border-2 border-slate-400 focus:ring-indigo-500 cursor-pointer">
                                                        </label>
                                                    </td>

                                                    <td class="py-3 px-4 text-slate-800 dark:text-slate-200 font-medium text-xs sm:text-sm leading-snug">
                                                        <span class="inline-flex items-center gap-2.5">
                                                            <span class="w-6 h-6 rounded-md flex items-center justify-center text-[11px] font-black shrink-0 transition-colors duration-100"
                                                                :class="sel == {{ $opt['id'] }}
                                                                    ? 'bg-indigo-600 text-white shadow-xs'
                                                                    : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'">
                                                                {{ $label }}
                                                            </span>
                                                            <span :class="sel == {{ $opt['id'] }} ? 'text-indigo-900 dark:text-indigo-100 font-semibold' : ''">
                                                                {{ $opt['option_text'] }}
                                                            </span>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer for PAPI -->
                        <div class="flex items-center justify-between p-4 border-t border-slate-100 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#070B14]">
                            <button type="button" wire:click="prevQuestion" {{ $currentPage === 0 ? 'disabled' : '' }}
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#1A2A4C] disabled:opacity-40 disabled:cursor-not-allowed transition shadow-2xs">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                <span>Halaman Sebelumnya</span>
                            </button>

                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">
                                Halaman {{ $currentPage + 1 }} dari {{ $totalPages }}
                            </span>

                            @if ($currentPage < $totalPages - 1)
                                <button type="button" wire:click="nextQuestion"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-xs">
                                    <span>Halaman Selanjutnya</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            @else
                                <button type="button" wire:click="openFinishModal"
                                    wire:loading.attr="disabled" wire:target="openFinishModal,finishTest"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white transition shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span wire:loading.remove wire:target="openFinishModal">Kirim Jawaban & Selesai</span>
                                    <span wire:loading wire:target="openFinishModal">Menyimpan...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @elseif ($currentQuestion)
                    @php
                        $selectedInitial = $answers[$currentQuestion['id']] ?? null;
                        $isMultipleChoice = ($currentQuestion['question_type'] === 'multiple_choice');
                    @endphp
                    <div id="soal-area" wire:key="soal-area-{{ $currentQuestion['id'] }}" 
                         x-data="{
                             selected: {{ json_encode($selectedInitial) }},
                             pick(optId) {
                                 this.selected = optId;
                                 $wire.saveChoiceAnswer({{ $currentQuestion['id'] }}, optId);
                             }
                         }"
                         class="bg-white dark:bg-[#0D1527] rounded-3xl border border-slate-200/80 dark:border-[#1D2E54] shadow-sm p-6 sm:p-8 space-y-6">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-[#1D2E54]">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-blue-600 dark:bg-[#93F514] text-white dark:text-black font-black text-xs">
                                    Soal No. {{ $currentQuestionIndex + 1 }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-semibold bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-[#1D2E54]">
                                    @if ($currentQuestion['question_type'] === 'multiple_choice')
                                        Pilihan Ganda
                                    @elseif ($currentQuestion['question_type'] === 'disc')
                                        Pernyataan DISC
                                    @elseif ($currentQuestion['question_type'] === 'papi_kostick')
                                        PAPI Kostick
                                    @else
                                        Uraian / Essay
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 dark:text-[#93F514] bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800">
                                Bobot: {{ $currentQuestion['points'] ?? 1 }} Poin
                            </span>
                        </div>

                        <!-- LAMPIRAN FILE ATAU GAMBAR SOAL (JIKA ADA) -->
                        @if (!empty($currentQuestion['image_path']))
                            @php
                                $filePath = $currentQuestion['image_path'];
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                $isPdf = ($ext === 'pdf');
                                $fileUrl = asset('storage/' . $filePath);
                            @endphp

                            @if ($isPdf)
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50/90 dark:bg-[#14203A] border border-rose-200 dark:border-rose-900/60 flex items-center justify-between gap-3 shadow-xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/70 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM9.5 17.5c-.28 0-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5s.5.22.5.5v4c0 .28-.22.5-.5.5zm2-4.5h.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H12v1c0 .28-.22.5-.5.5s-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5zm.5 2c.28 0 .5-.22.5-.5s-.22-.5-.5-.5H12v1h.5zm2.5-2h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1c-.28 0-.5-.22-.5-.5v-4c0-.28.22-.5.5-.5zm.5 3h.5c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5H15v2z" />
                                            </svg>
                                        </div>
                                        <div class="truncate">
                                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white block truncate">Dokumen Lampiran Soal (PDF)</span>
                                            <span class="text-[11px] text-slate-500 dark:text-[#93A5C9] block truncate">Klik untuk membaca materi studi kasus di tab baru</span>
                                        </div>
                                    </div>
                                    <a href="{{ $fileUrl }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-xs transition shrink-0" title="Buka dan baca dokumen PDF di tab baru">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>Buka di Tab Baru</span>
                                    </a>
                                </div>
                            @elseif ($isImage)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Gambar Lampiran Soal
                                        </span>
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-[#93F514] hover:underline">
                                            Buka Ukuran Penuh
                                        </a>
                                    </div>
                                    <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] p-2 text-center">
                                        <img src="{{ $fileUrl }}" alt="Lampiran Soal" class="max-h-80 mx-auto object-contain rounded-lg">
                                    </div>
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] flex items-center justify-between gap-3">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Lampiran Soal ({{ strtoupper($ext) }})</span>
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>Buka Dokumen</span>
                                    </a>
                                </div>
                            @endif
                        @endif

                        <!-- Pertanyaan Teks & Dokumen Lampiran Kasus -->
                        @php
                            $doc = $currentQuestion['metadata']['document'] ?? null;
                            $hasDokPlaceholder = str_contains($currentQuestion['question'], '[[DOK]]');
                            $qParts = $hasDokPlaceholder ? explode('[[DOK]]', $currentQuestion['question']) : [$currentQuestion['question']];
                        @endphp

                        <div class="soal-teks text-sm sm:text-base font-medium text-slate-900 dark:text-white whitespace-pre-line leading-relaxed select-none">
                            {{ trim($qParts[0]) }}
                        </div>

                        @if ($doc)
                            <div class="my-4 p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] shadow-xs overflow-x-auto text-xs">
                                <div class="flex flex-wrap items-start justify-between border-b border-slate-200 dark:border-[#1D2E54] pb-3 mb-3 gap-2">
                                    <div>
                                        @if(!empty($doc['from']))
                                            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 whitespace-pre-line leading-tight">{{ $doc['from'] }}</p>
                                        @endif
                                        <h4 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mt-1">{{ $doc['title'] ?? 'DOKUMEN' }}</h4>
                                    </div>
                                    @if(!empty($doc['meta']))
                                        <div class="text-right text-[11px] text-slate-600 dark:text-slate-300 space-y-0.5">
                                            @foreach($doc['meta'] as $metaItem)
                                                <p><span class="text-slate-400">{{ $metaItem[0] }}:</span> <strong>{{ $metaItem[1] }}</strong></p>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <table class="w-full text-left border-collapse">
                                    @if(!empty($doc['cols']))
                                        <thead>
                                            <tr class="border-b border-slate-200 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527]">
                                                @foreach($doc['cols'] as $cIdx => $col)
                                                    @php $isRight = isset($doc['al']) && ($doc['al'][$cIdx] ?? '') === 'r'; @endphp
                                                    <th class="p-2 font-bold text-slate-700 dark:text-slate-300 {{ $isRight ? 'text-right' : '' }}">{{ $col }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                    @endif
                                    <tbody>
                                        @foreach($doc['rows'] ?? [] as $row)
                                            <tr class="border-b border-slate-200/50 dark:border-[#1D2E54]/50">
                                                @foreach($row as $rIdx => $cell)
                                                    @php $isRight = isset($doc['al']) && ($doc['al'][$rIdx] ?? '') === 'r'; @endphp
                                                    <td class="p-2 text-slate-800 dark:text-slate-200 {{ $isRight ? 'text-right font-mono' : '' }}">{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    @if(!empty($doc['foot']))
                                        <tfoot>
                                            @foreach($doc['foot'] as $foot)
                                                <tr class="font-bold border-t border-slate-200 dark:border-[#1D2E54] bg-white/50 dark:bg-[#0D1527]/50">
                                                    <td colspan="{{ max(1, count($doc['cols'] ?? []) - 1) }}" class="p-2 text-right text-slate-700 dark:text-slate-300">{{ $foot[0] }}</td>
                                                    <td class="p-2 text-right font-mono text-slate-900 dark:text-white">{{ $foot[1] }}</td>
                                                </tr>
                                            @endforeach
                                        </tfoot>
                                    @endif
                                </table>
                                @if(!empty($doc['note']))
                                    <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400 italic">{{ $doc['note'] }}</p>
                                @endif
                            </div>
                        @endif

                        @if (count($qParts) > 1)
                            <div class="soal-teks text-sm sm:text-base font-semibold text-slate-900 dark:text-white whitespace-pre-line leading-relaxed select-none">
                                {{ trim($qParts[1]) }}
                            </div>
                        @endif

                        <!-- OPSI PILIHAN GANDA -->
                        @if ($currentQuestion['question_type'] === 'multiple_choice')
                            @php
                                $labels = ['A', 'B', 'C', 'D', 'E'];
                            @endphp
                            <div class="space-y-3 pt-2 soal-opsi"
                                 wire:key="mc-box-{{ $currentQuestion['id'] }}"
                                 @keydown.window="
                                     if (!showCalc && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) && !document.activeElement?.isContentEditable) {
                                         @foreach ($currentQuestion['options'] as $idx => $opt)
                                             if (['{{ strtolower($labels[$idx] ?? '') }}', '{{ $idx + 1 }}'].includes($event.key.toLowerCase())) {
                                                 $event.preventDefault();
                                                 pick({{ $opt['id'] }});
                                             }
                                         @endforeach
                                     }
                                 ">
                                @foreach ($currentQuestion['options'] as $idx => $opt)
                                    <div @click="pick({{ $opt['id'] }})"
                                        class="flex items-center gap-3.5 p-4 rounded-2xl border cursor-pointer transition-all duration-75 active:scale-[0.99] select-none"
                                        :class="selected == {{ $opt['id'] }}
                                            ? 'bg-blue-50/80 dark:bg-[#14203A] border-blue-500 dark:border-[#93F514] ring-2 ring-blue-500/20 dark:ring-[#93F514]/20 text-slate-900 dark:text-white font-semibold'
                                            : 'bg-slate-50/50 dark:bg-[#14203A]/40 border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-slate-200 hover:bg-slate-100/80 dark:hover:bg-[#14203A]'">
                                        <input type="radio" name="opt_{{ $currentQuestion['id'] }}" value="{{ $opt['id'] }}" :checked="selected == {{ $opt['id'] }}" class="sr-only" @click.stop>
                                        <span class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 transition-colors"
                                              :class="selected == {{ $opt['id'] }} ? 'bg-blue-600 dark:bg-[#93F514] text-white dark:text-black shadow-xs' : 'bg-slate-200 dark:bg-[#0D1527] text-slate-700 dark:text-slate-300'">
                                            {{ $labels[$idx] ?? ($idx + 1) }}
                                        </span>
                                        <span class="text-xs sm:text-sm flex-1 leading-snug">
                                            {{ $opt['option_text'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- OPSI PAPI KOSTICK (PILIHAN DUA PERNYATAAN A / B) -->
                        @if ($currentQuestion['question_type'] === 'papi_kostick')
                            @php
                                $letters = ['A', 'B'];
                            @endphp
                            <div class="space-y-4 pt-2"
                                 wire:key="papi-box-{{ $currentQuestion['id'] }}">
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9] font-medium italic">
                                    Pilihlah salah satu dari dua pernyataan di bawah ini yang paling menggambarkan diri Anda atau paling sesuai dengan diri Anda:
                                </p>
                                <div class="grid grid-cols-1 gap-3.5">
                                    @foreach ($currentQuestion['options'] as $idx => $opt)
                                        <div @click="pick({{ $opt['id'] }})"
                                            class="flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all duration-75 active:scale-[0.99] select-none"
                                            :class="selected == {{ $opt['id'] }}
                                                ? 'bg-blue-50/90 dark:bg-blue-950/40 border-blue-600 dark:border-[#93F514] ring-2 ring-blue-500/20 dark:ring-[#93F514]/20 text-slate-900 dark:text-white shadow-sm'
                                                : 'bg-white dark:bg-[#14203A]/40 border-slate-200 dark:border-[#1D2E54] text-slate-800 dark:text-slate-200 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-slate-50/80 dark:hover:bg-[#14203A]'">
                                            <input type="radio" name="opt_papi_{{ $currentQuestion['id'] }}" value="{{ $opt['id'] }}" :checked="selected == {{ $opt['id'] }}" class="sr-only" @click.stop>
                                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm shrink-0 transition-colors"
                                                  :class="selected == {{ $opt['id'] }} ? 'bg-blue-600 dark:bg-[#93F514] text-white dark:text-black shadow' : 'bg-slate-100 dark:bg-[#0D1527] text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-[#1D2E54]'">
                                                {{ $letters[$idx] ?? ($idx == 0 ? 'A' : 'B') }}
                                            </span>
                                            <span class="text-sm sm:text-base flex-1 leading-relaxed pt-1 select-none">
                                                {{ $opt['option_text'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- FORM ESSAY / URAIAN -->
                        @if ($currentQuestion['question_type'] === 'essay')
                            <div class="space-y-4 pt-2" wire:key="essay-question-{{ $currentQuestion['id'] }}">
                                <div class="space-y-2">
                                    <label for="essay_{{ $currentQuestion['id'] }}" class="block text-xs font-bold text-slate-700 dark:text-[#93A5C9]">
                                        Tuliskan Jawaban Uraian / Analisis Anda:
                                    </label>
                                    <textarea id="essay_{{ $currentQuestion['id'] }}"
                                        rows="6"
                                        wire:model.lazy="answers.{{ $currentQuestion['id'] }}"
                                        wire:change="submitEssayAnswer({{ $currentQuestion['id'] }})"
                                        placeholder="Ketikkan jawaban essay Anda secara lengkap dan terstruktur... (Jika menyertakan link video/Google Drive, cantumkan di sini)"
                                        class="w-full p-4 rounded-2xl bg-slate-50 dark:bg-[#14203A] border border-slate-200 dark:border-[#1D2E54] text-xs sm:text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:outline-none transition leading-relaxed"></textarea>
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 dark:text-[#93A5C9]">
                                        <span>Jawaban teks tersimpan otomatis saat Anda berpindah soal atau menekan tombol navigasi.</span>
                                    </div>
                                </div>

                                <!-- PERHATIAN UNTUK FILE BESAR / VIDEO VIA GOOGLE DRIVE / YOUTUBE -->
                                <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/95 dark:bg-amber-950/40 border-2 border-amber-300/80 dark:border-amber-700/60 shadow-sm flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 dark:bg-amber-400/20 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 space-y-2 text-amber-950 dark:text-amber-100">
                                        <div class="text-sm sm:text-base font-bold flex items-center gap-2 text-amber-900 dark:text-amber-200">
                                            <span>Petunjuk Lampiran Dokumen & Link Video</span>
                                        </div>
                                        <ul class="list-disc ml-4 space-y-1 text-xs sm:text-sm text-amber-900/90 dark:text-amber-200">
                                            <li><strong>File Dokumen (Kolom Kiri):</strong> Unggah file PDF, Word, Excel, atau Gambar dengan batas <strong>maksimal 10 MB</strong>.</li>
                                            <li><strong>Tautan Video / Eksternal (Kolom Kanan):</strong> Jika jawaban berupa <strong>Video</strong> atau berkas besar, unggah ke <strong>Google Drive</strong> atau <strong>YouTube</strong> lalu tempel link-nya di kolom tautan.</li>
                                        </ul>
                                        <div class="p-2.5 sm:p-3 rounded-xl bg-amber-100/80 dark:bg-amber-900/50 border border-amber-300/60 dark:border-amber-700/60 text-xs sm:text-sm text-amber-900 dark:text-amber-200 font-medium flex items-start gap-2.5">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span>Pastikan akses link diatur ke <strong class="underline underline-offset-2">"Siapa saja yang memiliki link"</strong> (Google Drive) atau <strong class="underline underline-offset-2">"Unlisted"</strong> (YouTube) agar penilai dapat membukanya.</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- UPLOAD FILE LAMPIRAN & FORM KHUSUS LINK (1 FILE & 1 LINK UNTUK SELURUH SOAL URAIAN) -->
                                <div class="p-5 rounded-2xl bg-slate-50/80 dark:bg-[#14203A]/70 border border-slate-200 dark:border-[#1D2E54] space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 dark:text-white">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <span>Lampiran File & Tautan Jawaban Uraian (Opsional)</span>
                                        </div>
                                        <span class="text-[11px] font-medium text-slate-500 dark:text-[#93A5C9] bg-white dark:bg-[#0D1527] px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-[#1D2E54]">
                                            1 File & 1 Tautan untuk Seluruh Soal Uraian
                                        </span>
                                    </div>

                                    {{-- DUA KOLOM BERSANDING: UPLOAD FILE & FORM KHUSUS LINK --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                        {{-- KOLOM 1: UPLOAD FILE FISIK (MAKS 10MB) --}}
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span>1. Unggah File Fisik (Maks. 10 MB)</span>
                                                </label>
                                                @if ($essayAttachment)
                                                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-[#93F514] flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        File Tersimpan
                                                    </span>
                                                @endif
                                            </div>

                                            @error('essayFile')
                                                <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>{{ $message }}</span>
                                                </div>
                                            @enderror

                                            @if ($essayAttachment)
                                                <!-- PREVIEW FILE YANG SUDAH DIUNGGAH -->
                                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-blue-50/70 dark:bg-[#14203A] border border-blue-200 dark:border-[#1D2E54] transition">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-10 h-10 rounded-xl bg-blue-600/10 dark:bg-[#1D2E54] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </div>
                                                        <div class="truncate">
                                                            <a href="{{ $essayAttachment['url'] }}" target="_blank" class="text-xs font-semibold text-blue-700 dark:text-[#93F514] hover:underline truncate block">
                                                                {{ $essayAttachment['name'] }}
                                                            </a>
                                                            <span class="text-[11px] text-slate-500 dark:text-[#93A5C9]">
                                                                 @if(!empty($essayAttachment['size']))
                                                                     {{ $essayAttachment['size'] >= 1048576 ? round($essayAttachment['size'] / 1048576, 2) . ' MB' : round($essayAttachment['size'] / 1024, 1) . ' KB' }} •
                                                                 @endif
                                                                 File Tersimpan
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0 ml-2">
                                                        <a href="{{ $essayAttachment['url'] }}" target="_blank" class="p-2 text-xs font-medium text-blue-600 dark:text-[#93F514] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg transition" title="Buka / Preview File">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                            </svg>
                                                        </a>
                                                        <button type="button" wire:click="removeEssayAttachment" wire:confirm="Apakah Anda yakin ingin menghapus lampiran file ini?" class="p-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus File">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <!-- INPUT UPLOAD FILE BARU (AUTO SAVE) -->
                                                <div>
                                                    <label @click="openFileDialog()" class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed border-slate-300 dark:border-[#1D2E54] bg-white dark:bg-[#0D1527] hover:border-blue-400 dark:hover:border-[#93F514] cursor-pointer transition group">
                                                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-[#14203A] text-blue-600 dark:text-[#93F514] flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 truncate">
                                                            <span class="text-xs text-slate-700 dark:text-slate-300 font-medium">
                                                                Pilih atau drag file dokumen (Maks. 10 MB)
                                                            </span>
                                                            <span class="block text-[10px] text-slate-400 dark:text-[#93A5C9]">File otomatis tersimpan setelah dipilih</span>
                                                        </div>
                                                        <input type="file" wire:model="essayFile" class="sr-only">
                                                    </label>
                                                </div>

                                                <!-- Livewire Uploading & Saving Indicator -->
                                                <div wire:loading wire:target="essayFile" class="p-3 rounded-xl bg-blue-50/70 dark:bg-[#14203A] border border-blue-200 dark:border-[#1D2E54] text-xs text-blue-700 dark:text-[#93F514] flex items-center gap-2.5">
                                                    <svg class="animate-spin w-4 h-4 text-blue-600 dark:text-[#93F514] shrink-0" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                                    </svg>
                                                    <span class="font-medium">Sedang mengunggah dan menyimpan file lampiran...</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- KOLOM 2: FORM KHUSUS LINK (GOOGLE DRIVE / YOUTUBE) --}}
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-blue-600 dark:text-[#93F514]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                    <span>2. Tautan Video / File Eksternal (Opsional)</span>
                                                </label>
                                                @if ($essayLink)
                                                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-[#93F514] flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Tautan Tersimpan
                                                    </span>
                                                @endif
                                            </div>

                                            @error('essayLink')
                                                <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>{{ $message }}</span>
                                                </div>
                                            @enderror

                                            @if (session()->has('link_message'))
                                                <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>{{ session('link_message') }}</span>
                                                </div>
                                            @endif

                                            @if ($essayLink)
                                                @php
                                                    $isGdrive = str_contains($essayLink, 'drive.google.com');
                                                    $isYt = str_contains($essayLink, 'youtube.com') || str_contains($essayLink, 'youtu.be');
                                                @endphp
                                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-blue-50/70 dark:bg-[#14203A] border border-blue-200 dark:border-[#1D2E54] transition">
                                                    <div class="flex items-center gap-3 min-w-0">
                                                        <div class="w-10 h-10 rounded-xl {{ $isGdrive ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : ($isYt ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-blue-100 text-blue-700 dark:bg-[#1D2E54] dark:text-[#93F514]') }} flex items-center justify-center shrink-0">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                            </svg>
                                                        </div>
                                                        <div class="truncate">
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $isGdrive ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' : ($isYt ? 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300') }}">
                                                                    {{ $isGdrive ? 'Google Drive' : ($isYt ? 'YouTube' : 'Tautan Eksternal') }}
                                                                </span>
                                                            </div>
                                                            <a href="{{ $essayLink }}" target="_blank" class="text-xs font-mono text-slate-600 dark:text-slate-300 hover:underline truncate block mt-0.5">
                                                                {{ $essayLink }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0 ml-2">
                                                        <a href="{{ $essayLink }}" target="_blank" class="p-2 text-xs font-medium text-blue-600 dark:text-[#93F514] hover:bg-blue-100 dark:hover:bg-[#1D2E54] rounded-lg transition" title="Uji / Buka Tautan">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                            </svg>
                                                        </a>
                                                        <button type="button" wire:click="removeEssayLink" wire:confirm="Hapus tautan ini?" class="p-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus Tautan">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center gap-2">
                                                        <div class="relative flex-1">
                                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                                </svg>
                                                            </div>
                                                            <input type="url"
                                                                wire:model.lazy="essayLink"
                                                                wire:keydown.enter.prevent="saveEssayLink"
                                                                placeholder="https://drive.google.com/... atau https://youtu.be/..."
                                                                class="w-full pl-9 pr-3 py-2.5 rounded-xl bg-white dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-[#93F514] focus:outline-none transition font-mono">
                                                        </div>
                                                        <button type="button" wire:click="saveEssayLink"
                                                            class="px-3.5 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-500 dark:bg-[#93F514] dark:text-black dark:hover:bg-[#82dc11] rounded-xl shadow-xs transition shrink-0 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            <span>Simpan Link</span>
                                                        </button>
                                                    </div>
                                                    <span class="block text-[10px] text-slate-400 dark:text-[#93A5C9]">Ketik/tempel link lalu tekan Enter atau klik Simpan Link.</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- DISC QUESTION TYPE (LAYOUT HORIZONTAL DENGAN ATURAN: BARIS YANG SAMA TIDAK BISA DIKLIK SEBERANGNYA) -->
                        @if ($currentQuestion['question_type'] === 'disc')
                            @php
                                $mostOpt = $answers[$currentQuestion['id']]['most'] ?? null;
                                $leastOpt = $answers[$currentQuestion['id']]['least'] ?? null;
                            @endphp
                            <div class="space-y-4 pt-2" 
                                 wire:key="disc-box-{{ $currentQuestion['id'] }}"
                                 wire:ignore
                                 x-data="{
                                     discMost: {{ $mostOpt ?? 'null' }},
                                     discLeast: {{ $leastOpt ?? 'null' }},
                                     selectDisc(optId, type) {
                                         if (type === 'most' && (this.discLeast == optId || this.discMost == optId)) return;
                                         if (type === 'least' && (this.discMost == optId || this.discLeast == optId)) return;
                                         if (type === 'most') this.discMost = optId;
                                         else this.discLeast = optId;
                                         $wire.saveDiscAnswer({{ $currentQuestion['id'] }}, optId, type);
                                     }
                                 }">
                                <!-- Instruction Header -->
                                <div class="flex items-center justify-between text-xs font-semibold px-1 text-slate-600 dark:text-[#93A5C9]">
                                    <span>Pilihlah <strong>1 pernyataan P</strong> (Paling Menggambarkan) dan <strong>1 pernyataan K</strong> (Kurang Menggambarkan).</span>
                                    <div class="flex items-center gap-1.5 font-mono text-[11px]">
                                        <span class="px-2.5 py-0.5 rounded font-bold"
                                              :class="discMost ? 'bg-amber-400 text-amber-950' : 'bg-slate-100 dark:bg-[#14203A] text-slate-400 dark:text-[#93A5C9]'">
                                            P: <span x-text="discMost ? '✓' : '-'"></span>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded font-bold"
                                              :class="discLeast ? 'bg-sky-400 text-sky-950' : 'bg-slate-100 dark:bg-[#14203A] text-slate-400 dark:text-[#93A5C9]'">
                                            K: <span x-text="discLeast ? '✓' : '-'"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Custom DISC Table Form matching Screenshot -->
                                <div class="overflow-x-auto rounded-xl border-2 border-slate-900 dark:border-[#1D2E54] shadow-sm">
                                    <table class="w-full text-xs border-collapse text-left">
                                        <thead>
                                            <tr class="font-extrabold text-sm border-b-2 border-slate-900 dark:border-[#1D2E54] text-center">
                                                <th class="w-14 py-2.5 px-3 bg-slate-300 dark:bg-[#14203A] text-slate-900 dark:text-white border-r-2 border-slate-900 dark:border-[#1D2E54]">No.</th>
                                                <th class="w-14 py-2.5 px-3 bg-amber-400 text-slate-900 border-r-2 border-slate-900 dark:border-[#1D2E54]">P</th>
                                                <th class="w-14 py-2.5 px-3 bg-sky-400 text-slate-900 border-r-2 border-slate-900 dark:border-[#1D2E54]">K</th>
                                                <th class="py-2.5 px-4 bg-slate-800 dark:bg-[#070B14] text-white font-bold text-left tracking-wide">Gambaran Diri</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y-2 divide-slate-900 dark:divide-[#1D2E54] font-medium text-xs">
                                            @foreach ($currentQuestion['options'] as $idx => $opt)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-[#14203A]/60 transition"
                                                    :class="{
                                                        'bg-amber-50/40 dark:bg-amber-950/20': discMost == {{ $opt['id'] }},
                                                        'bg-sky-50/40 dark:bg-sky-950/20': discLeast == {{ $opt['id'] }}
                                                    }"
                                                    wire:key="opt-row-{{ $currentQuestion['id'] }}-{{ $opt['id'] }}">
                                                    @if ($idx === 0)
                                                        <td rowspan="{{ count($currentQuestion['options']) }}" class="text-center font-black text-lg bg-slate-200 dark:bg-[#14203A] text-slate-900 dark:text-white border-r-2 border-slate-900 dark:border-[#1D2E54]">
                                                            {{ $currentQuestionIndex + 1 }}
                                                        </td>
                                                    @endif

                                                    <!-- Kolom P (Kuning / Amber) -->
                                                    <td class="text-center border-r-2 border-slate-900 dark:border-[#1D2E54] p-0"
                                                        :class="discLeast == {{ $opt['id'] }} ? 'bg-slate-200 dark:bg-[#0D1527] opacity-40 cursor-not-allowed' : 'bg-amber-400/90'">
                                                        <label class="w-full h-11 flex items-center justify-center"
                                                               :class="discLeast == {{ $opt['id'] }} ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-amber-500/80 transition'">
                                                            <input type="radio" 
                                                                   name="disc_most_{{ $currentQuestion['id'] }}" 
                                                                   value="{{ $opt['id'] }}" 
                                                                   :checked="discMost == {{ $opt['id'] }}"
                                                                   :disabled="discLeast == {{ $opt['id'] }}"
                                                                   @click="selectDisc({{ $opt['id'] }}, 'most')"
                                                                   class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 border-2 border-slate-900"
                                                                   :class="discLeast == {{ $opt['id'] }} ? 'cursor-not-allowed opacity-30' : 'cursor-pointer'">
                                                        </label>
                                                    </td>

                                                    <!-- Kolom K (Biru / Sky) -->
                                                    <td class="text-center border-r-2 border-slate-900 dark:border-[#1D2E54] p-0"
                                                        :class="discMost == {{ $opt['id'] }} ? 'bg-slate-200 dark:bg-[#0D1527] opacity-40 cursor-not-allowed' : 'bg-sky-400/90'">
                                                        <label class="w-full h-11 flex items-center justify-center"
                                                               :class="discMost == {{ $opt['id'] }} ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-sky-500/80 transition'">
                                                            <input type="radio" 
                                                                   name="disc_least_{{ $currentQuestion['id'] }}" 
                                                                   value="{{ $opt['id'] }}" 
                                                                   :checked="discLeast == {{ $opt['id'] }}"
                                                                   :disabled="discMost == {{ $opt['id'] }}"
                                                                   @click="selectDisc({{ $opt['id'] }}, 'least')"
                                                                   class="w-5 h-5 text-rose-600 focus:ring-rose-500 focus:ring-offset-0 border-2 border-slate-900"
                                                                   :class="discMost == {{ $opt['id'] }} ? 'cursor-not-allowed opacity-30' : 'cursor-pointer'">
                                                        </label>
                                                    </td>

                                                    <!-- Kolom Teks Gambaran Diri -->
                                                    <td class="py-2.5 px-4 text-slate-900 dark:text-slate-100 font-semibold bg-white dark:bg-[#0D1527] text-xs sm:text-sm">
                                                        {{ $opt['option_text'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Navigation Buttons -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-100 dark:border-[#1D2E54] gap-3">
                            <button type="button" 
                                @click="$wire.prevQuestion({{ $currentQuestion['id'] }}, typeof selected !== 'undefined' ? selected : null)" 
                                {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                                wire:loading.attr="disabled" wire:target="prevQuestion"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-300 dark:border-[#1D2E54] bg-white dark:bg-[#14203A] text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#1A2A4C] disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer">
                                <svg class="w-4 h-4 text-slate-500 dark:text-[#93A5C9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                <span>Sebelumnya</span>
                            </button>

                            @if ($currentQuestionIndex < count($questions) - 1)
                                <button type="button" 
                                    @click="$wire.nextQuestion({{ $currentQuestion['id'] }}, typeof selected !== 'undefined' ? selected : null)"
                                    wire:loading.attr="disabled" wire:target="nextQuestion"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 active:scale-95 text-white text-xs font-bold transition shadow-xs disabled:opacity-50 cursor-pointer">
                                    <span>Selanjutnya</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @else
                                <button type="button" 
                                    @click="$wire.openFinishModal({{ $currentQuestion['id'] }}, typeof selected !== 'undefined' ? selected : null)"
                                    wire:loading.attr="disabled" wire:target="openFinishModal,finishTest"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black text-xs font-bold shadow-md shadow-emerald-600/20 dark:shadow-[#93F514]/20 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span wire:loading.remove wire:target="openFinishModal">Kirim Jawaban & Selesai</span>
                                    <span wire:loading wire:target="openFinishModal">Menyimpan...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Side: Nomor Soal Grid & Submit Button (1 Col) -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white dark:bg-[#0D1527] rounded-2xl border border-slate-200/80 dark:border-[#1D2E54] p-5 space-y-4 sticky top-24 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-[#1D2E54]">
                        <h3 class="text-xs font-semibold text-slate-900 dark:text-white uppercase tracking-wider">
                            Navigasi Soal
                        </h3>
                        <span class="text-xs font-medium text-slate-500 dark:text-[#93A5C9]">
                            {{ $completedCount }} / {{ $totalQuestions }} Terisi
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-[#93A5C9]">
                            <span>Kelengkapan</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-100 dark:bg-[#14203A] rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-600 dark:bg-[#93F514] transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Number Grid -->
                    <div class="grid grid-cols-5 gap-1.5">
                        @foreach ($questions as $idx => $q)
                            @php
                                $status = 'unanswered';
                                if ($q['question_type'] === 'disc') {
                                    $hasMost = !empty($answers[$q['id']]['most']);
                                    $hasLeast = !empty($answers[$q['id']]['least']);
                                    if ($hasMost && $hasLeast) {
                                        $status = 'completed';
                                    } elseif ($hasMost || $hasLeast) {
                                        $status = 'partial';
                                    }
                                } elseif ($q['question_type'] === 'multiple_choice' || $q['question_type'] === 'papi_kostick') {
                                    $status = (!empty($answers[$q['id']])) ? 'completed' : 'unanswered';
                                } elseif ($q['question_type'] === 'essay') {
                                    $firstEssay = collect($questions)->firstWhere('question_type', 'essay');
                                    $isFirstEssay = ($firstEssay && $firstEssay['id'] == $q['id']);
                                    $hasText = !empty($answers[$q['id']]) && trim($answers[$q['id']]) !== '';
                                    $hasFileOrLink = $isFirstEssay && ($essayAttachment || !empty($essayLink));
                                    $status = ($hasText || $hasFileOrLink) ? 'completed' : 'unanswered';
                                }
                                $isPapiPage = ($q['question_type'] === 'papi_kostick');
                                $isOnCurrentPage = ($idx >= $pageStart && $idx < $pageStart + $questionsPerPage);
                                $isCurrent = $isPapiPage ? $isOnCurrentPage : ($currentQuestionIndex === $idx);
                            @endphp
                            <button type="button" wire:key="grid-q-{{ $idx }}" 
                                @click="$wire.selectQuestion({{ $idx }}, typeof selected !== 'undefined' ? {{ $currentQuestion['id'] ?? 'null' }} : null, typeof selected !== 'undefined' ? selected : null)"
                                class="h-8 rounded-lg text-xs font-medium flex items-center justify-center transition border cursor-pointer {{ $isCurrent ? 'ring-2 ring-blue-500 dark:ring-[#93F514] ring-offset-1 border-blue-500 dark:border-[#93F514]' : '' }} {{ $status === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 dark:bg-[#93F514] dark:text-black dark:border-[#93F514]' : ($status === 'partial' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700 font-semibold' : 'bg-slate-50 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-[#1D2E54] hover:bg-slate-100 dark:hover:bg-[#1A2A4C]') }}"
                                title="Soal No. {{ $idx + 1 }}">
                                {{ $idx + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Status Legend -->
                    <div class="pt-3 border-t border-slate-100 dark:border-[#1D2E54] space-y-1.5 text-[11px] text-slate-500 dark:text-[#93A5C9]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 dark:bg-[#93F514] shrink-0"></span>
                            <span>Sudah Lengkap {{ collect($questions)->contains('question_type', 'disc') ? '(P & K)' : '' }}</span>
                        </div>
                        @if (collect($questions)->contains('question_type', 'disc'))
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Kurang P atau K</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                            <span>Belum Dijawab</span>
                        </div>
                        @if (collect($questions)->contains('question_type', 'papi_kostick'))
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full border-2 border-blue-500 dark:border-[#93F514] shrink-0"></span>
                                <span>Halaman Saat Ini</span>
                            </div>
                        @endif
                    </div>

                    @if ($completedCount < $totalQuestions)
                        <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300">
                            Masih ada {{ $totalQuestions - $completedCount }} butir soal yang belum lengkap.
                        </div>
                    @endif

                    <button type="button" 
                        @click="$wire.openFinishModal(typeof selected !== 'undefined' ? {{ $currentQuestion['id'] ?? 'null' }} : null, typeof selected !== 'undefined' ? selected : null)"
                        wire:loading.attr="disabled" wire:target="openFinishModal,finishTest"
                        class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 mt-2 shadow-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove wire:target="openFinishModal">Selesaikan Ujian</span>
                        <span wire:loading wire:target="openFinishModal">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Confirmation Modal (Replacing Browser Native Confirm) -->
        <div x-show="showConfirmModal" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-slate-950/75 backdrop-blur-sm" 
                     @click="showConfirmModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Dialog Panel -->
                <div x-show="showConfirmModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-[#0D1527] rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-[#1D2E54] p-6 space-y-4">
                    
                    @if ($completedCount === $totalQuestions)
                        <!-- State 1: Semua Soal Sudah Terisi Lengkap -->
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center text-emerald-600 dark:text-[#93F514] shrink-0 border border-emerald-200 dark:border-emerald-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    Selesaikan Ujian?
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                    Seluruh <strong>{{ $totalQuestions }} butir soal</strong> telah terisi. Setelah dikirim, jawaban tidak dapat diubah kembali.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-[#1D2E54]">
                            <button type="button" 
                                    @click="showConfirmModal = false" 
                                    wire:click="closeFinishModal"
                                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="showConfirmModal = false; $wire.finishTest(false, typeof selected !== 'undefined' ? {{ $currentQuestion['id'] ?? 'null' }} : null, typeof selected !== 'undefined' ? selected : null, typeof violations !== 'undefined' ? violations : null)" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black rounded-xl transition shadow-sm cursor-pointer">
                                <span wire:loading.remove wire:target="finishTest">Ya, Selesaikan</span>
                                <span wire:loading wire:target="finishTest">Menyimpan...</span>
                            </button>
                        </div>
                    @else
                        <!-- State 2: Ada Soal Belum Lengkap -->
                        @php
                            $isPsychometric = collect($questions)->contains(function($q) {
                                return in_array($q['question_type'] ?? '', ['disc', 'papi_kostick']);
                            });
                        @endphp
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center text-amber-600 dark:text-amber-400 shrink-0 border border-amber-200 dark:border-amber-800/80">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 dark:text-white">
                                    Jawaban Belum Lengkap
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-[#93A5C9] mt-1 leading-relaxed">
                                    Masih terdapat <strong>{{ $totalQuestions - $completedCount }} butir soal</strong> yang belum dijawab / belum lengkap.
                                    @if ($isPsychometric)
                                        Seluruh butir soal psikometri (DISC / PAPI Kostick) wajib terisi lengkap sebelum mengirimkan asesmen.
                                    @else
                                        Butir soal yang tidak dijawab akan dihitung 0 poin.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-[#1D2E54]">
                            <button type="button" 
                                    @click="showConfirmModal = false" 
                                    wire:click="closeFinishModal"
                                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-[#14203A] dark:hover:bg-[#1A2A4C] border border-slate-200/80 dark:border-[#1D2E54] rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="showConfirmModal = false; $wire.goToFirstUnanswered()" 
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition shadow-xs cursor-pointer">
                                <span>Lengkapi Jawaban</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            @if (!$isPsychometric)
                                <button type="button" 
                                        @click="showConfirmModal = false; $wire.finishTest(false, typeof selected !== 'undefined' ? {{ $currentQuestion['id'] ?? 'null' }} : null, typeof selected !== 'undefined' ? selected : null, typeof violations !== 'undefined' ? violations : null)" 
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl transition shadow-xs cursor-pointer">
                                    <span wire:loading.remove wire:target="finishTest">Tetap Kumpulkan</span>
                                    <span wire:loading wire:target="finishTest">Menyimpan...</span>
                                </button>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Jendela alat bantu di-ignore Livewire agar re-render tidak me-reset posisi, ukuran, dan status tampilnya --}}
        <div wire:ignore>
        <!-- FLOATING WINDOW: EXCEL SPREADSHEET (BISA DISERET & DIATUR UKURAN) -->
        <div data-floating-tool="excel"
             x-show="showExcel"
             x-cloak
             @mousedown="bringToFront('excel')"
             :style="'position: fixed; top: ' + winPos.excel.y + 'px; left: ' + winPos.excel.x + 'px; width: ' + winSize.excel.w + 'px; height: ' + winSize.excel.h + 'px; z-index: ' + toolZ('excel') + '; ' + (showExcel ? 'display: flex;' : 'display: none !important;')"
             style="position: fixed; display: none;"
             class="max-w-[96vw] max-h-[92vh] bg-white dark:bg-[#0D1527] border-2 border-emerald-500/80 dark:border-emerald-500/60 rounded-2xl shadow-2xl flex-col overflow-hidden select-none relative">
            <!-- Header (Drag Handle) -->
            <div @mousedown="startDrag($event, 'excel')"
                 @touchstart="startDrag($event, 'excel')"
                 class="flex items-center justify-between px-4 py-2.5 bg-emerald-600 text-white cursor-grab active:cursor-grabbing select-none shrink-0 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-bold tracking-wide">Lembar Kerja Excel (FAT CBT)</span>
                    <span class="text-[10px] bg-emerald-700 px-1.5 py-0.5 rounded text-emerald-100 font-mono">Tahan & Seret</span>
                </div>
                <div class="flex items-center gap-1.5" @mousedown.stop>
                    <button type="button" @click="clearExcel()" 
                        class="px-2.5 py-1 text-[10px] font-bold rounded-md transition cursor-pointer"
                        :class="confirmClearExcel ? 'bg-rose-600 text-white animate-pulse' : 'bg-emerald-800 hover:bg-rose-600 text-white'"
                        x-text="confirmClearExcel ? 'Yakin Hapus? Klik Sekali Lagi' : 'Bersihkan Lembar'"
                        title="Hapus seluruh isi lembar kerja">
                    </button>
                    <button type="button" @click="closeTool('excel')" class="p-1 hover:bg-emerald-700 rounded-md transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Formula Bar (fx) -->
            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-[#14203A] border-b border-slate-200 dark:border-[#1D2E54] text-xs font-mono shrink-0">
                <span class="px-2 py-0.5 rounded bg-white dark:bg-[#0D1527] border border-slate-300 dark:border-[#1D2E54] font-bold text-emerald-600 min-w-[42px] text-center" x-text="selectedCell">A1</span>
                <span class="text-slate-400 font-bold italic">fx</span>
                <input type="text" x-model="activeFormula" @input="updateFromFormulaBar()"
                       placeholder="Ketik teks, angka, atau rumus (misal: =SUM(A1:A5) atau =A1+B1)"
                       class="flex-1 px-2 py-0.5 bg-white dark:bg-[#0D1527] border border-slate-200 dark:border-[#1D2E54] rounded text-slate-800 dark:text-slate-100 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <button type="button" @click="addExcelRow()" class="px-2 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[11px] font-bold hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition cursor-pointer">
                    + Baris
                </button>
            </div>

            <!-- Spreadsheet Grid Table -->
            <div class="overflow-auto flex-1 h-full min-h-[140px] text-xs font-mono">
                <table class="w-full border-collapse select-none">
                    <thead class="sticky top-0 bg-slate-100 dark:bg-[#14203A] z-10">
                        <tr>
                            <th class="w-10 py-1 px-1.5 text-center text-[10px] font-bold text-slate-500 dark:text-slate-400 border border-slate-300 dark:border-[#1D2E54]">#</th>
                            <template x-for="col in excelCols" :key="col">
                                <th class="w-24 py-1 px-2 text-center text-[11px] font-bold text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-[#1D2E54]" x-text="col"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="r in excelRows" :key="r">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#14203A]/30">
                                <td class="text-center text-[10px] font-bold text-slate-400 border border-slate-200 dark:border-[#1D2E54] bg-slate-50 dark:bg-[#14203A]/60 py-1" x-text="r"></td>
                                <template x-for="col in excelCols" :key="col">
                                    <td class="border border-slate-200 dark:border-[#1D2E54] p-0 relative"
                                        :class="selectedCell === `${col}${r}` ? 'ring-2 ring-emerald-500 z-1 bg-emerald-50/30 dark:bg-emerald-950/20' : ''"
                                        @click="selectExcelCell(`${col}${r}`)">
                                        <input type="text"
                                               :value="editingCell === `${col}${r}` ? (excelCells[`${col}${r}`] || '') : evalCell(excelCells[`${col}${r}`] || '')"
                                               @focus="editingCell = `${col}${r}`; selectExcelCell(`${col}${r}`)"
                                               @blur="editingCell = null"
                                               @input="setCell(`${col}${r}`, $event.target.value); activeFormula = $event.target.value"
                                               class="w-full h-7 px-2 py-1 text-xs font-mono bg-transparent text-slate-900 dark:text-white border-0 outline-none focus:bg-white dark:focus:bg-[#070B14]">
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Footer Status Bar -->
            <div class="px-3 py-1.5 bg-slate-50 dark:bg-[#091124] border-t border-slate-200 dark:border-[#1D2E54] flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 shrink-0">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tersimpan otomatis di browser</span>
                </span>
                <span class="text-[10px] text-slate-400 italic">Dukung rumus: =SUM(A1:A5), =AVERAGE(A1:A5), =A1+B1, =A1*0.11</span>
            </div>

            <!-- Corner Resize Handle (Tahan & Seret Sudut untuk Mengubah Ukuran) -->
            <div @mousedown.stop="startResize($event, 'excel')"
                 @touchstart.stop="startResize($event, 'excel')"
                 @dblclick.stop="resetSize('excel')"
                 style="touch-action: none;"
                 class="absolute bottom-1 right-1 w-4 h-4 cursor-se-resize flex items-center justify-center text-emerald-400 hover:text-emerald-300 transition select-none z-20"
                 title="Tahan & seret sudut ini untuk memperbesar/memperkecil lembar Excel (Klik 2x untuk reset)">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 19L11 19M19 19L19 11M19 14L14 19" />
                </svg>
            </div>
        </div>

        <!-- FLOATING WINDOW: KALKULATOR UJIAN (BISA DISERET & DIATUR UKURAN) -->
        <div data-floating-tool="calc"
             x-show="showCalc" 
             x-cloak
             @mousedown="bringToFront('calc')"
             :style="'position: fixed; top: ' + winPos.calc.y + 'px; left: ' + winPos.calc.x + 'px; width: ' + winSize.calc.w + 'px; height: ' + winSize.calc.h + 'px; min-width: 310px; min-height: 440px; z-index: ' + toolZ('calc') + '; ' + (showCalc ? 'display: flex;' : 'display: none !important;')"
             style="position: fixed; display: none; min-width: 310px; min-height: 440px;"
             class="fixed min-w-[310px] min-h-[440px] max-w-[96vw] max-h-[92vh] bg-white dark:bg-[#0D1527] border-2 border-amber-400/80 dark:border-amber-400/60 rounded-2xl shadow-2xl p-3.5 space-y-2 flex-col overflow-hidden select-none relative">
            <!-- Header (Drag Handle) -->
            <div @mousedown="startDrag($event, 'calc')"
                 @touchstart.passive="false"
                 @touchstart="startDrag($event, 'calc')"
                 style="touch-action: none;"
                 class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-[#1D2E54] cursor-grab active:cursor-grabbing select-none shrink-0">
                <div class="flex items-center gap-2">
                    <!-- Drag Handle dots icon -->
                    <svg class="w-3.5 h-3.5 text-amber-400/80 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="8" cy="6" r="2"/><circle cx="16" cy="6" r="2"/>
                        <circle cx="8" cy="12" r="2"/><circle cx="16" cy="12" r="2"/>
                        <circle cx="8" cy="18" r="2"/><circle cx="16" cy="18" r="2"/>
                    </svg>
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    <span class="text-xs font-bold text-slate-900 dark:text-white">Kalkulator Ujian</span>
                    <span class="text-[10px] text-amber-500 font-mono bg-amber-50 dark:bg-amber-950/40 px-1.5 py-0.5 rounded font-semibold border border-amber-400/20">Tahan & Geser</span>
                </div>
                <button type="button" @click="closeTool('calc')" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Tape Riwayat Hitungan (Dengan tombol Bersihkan Riwayat) -->
            <div class="flex-1 flex flex-col min-h-0 space-y-1">
                <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium shrink-0">
                    <span>Riwayat Hitungan:</span>
                    <button type="button" @click="clearCalcTape()" 
                        class="text-rose-500 hover:underline flex items-center gap-1 font-semibold cursor-pointer transition text-[10px]"
                        :class="confirmClearCalcTape ? 'text-rose-600 font-extrabold animate-pulse' : ''"
                        title="Hapus seluruh riwayat dan bersihkan kalkulator">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span x-text="confirmClearCalcTape ? 'Yakin Hapus? Klik Sekali Lagi' : 'Bersihkan Riwayat'">Bersihkan Riwayat</span>
                    </button>
                </div>
                <div class="flex-1 min-h-[44px] overflow-y-auto text-[10px] font-mono text-slate-500 dark:text-slate-400 space-y-0.5 border border-slate-100 dark:border-[#1D2E54] bg-slate-50/60 dark:bg-[#14203A]/40 rounded-lg p-1.5 text-right">
                    <template x-if="calcTape.length === 0">
                        <span class="text-slate-400 italic block text-center py-2">Belum ada riwayat hitungan...</span>
                    </template>
                    <template x-for="(line, idx) in calcTape" :key="idx">
                        <div @click="useCalcResult(line)" class="cursor-pointer hover:text-amber-500 transition py-0.5 border-b border-slate-100/50 dark:border-[#1D2E54]/30" title="Klik untuk gunakan hasil ini" x-text="line"></div>
                    </template>
                </div>
            </div>

            <!-- Display (Atas: Rumus / Soal, Bawah: Hasil) -->
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#14203A] border border-slate-200/80 dark:border-[#1D2E54] text-right shrink-0 space-y-0.5">
                <div class="min-h-[16px] text-xs font-mono font-medium text-slate-500 dark:text-slate-400 truncate tracking-wide" x-text="calcExpr || '\u00A0'"></div>
                <div class="text-2xl font-black font-mono text-slate-900 dark:text-white truncate leading-tight tracking-tight" x-text="calcDisplay">0</div>
            </div>

            <!-- Keypad -->
            <div class="grid grid-cols-4 gap-1 text-xs font-bold font-mono shrink-0">
                <button type="button" @click="pressCalc('C')" class="h-9 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition cursor-pointer flex items-center justify-center">C</button>
                <button type="button" @click="pressCalc('BS')" class="h-9 rounded-xl bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">⌫</button>
                <button type="button" @click="pressCalc('NEG')" class="h-9 rounded-xl bg-slate-100 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">±</button>
                <button type="button" @click="pressCalc('/')" class="h-9 rounded-xl text-blue-600 dark:text-[#93F514] font-black text-sm hover:bg-blue-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center" :class="calcExpr.trimEnd().endsWith('÷') ? 'ring-2 ring-emerald-500 bg-blue-100 dark:bg-emerald-950/60' : 'bg-blue-50 dark:bg-[#14203A]'">÷</button>

                <button type="button" @click="pressCalc('7')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">7</button>
                <button type="button" @click="pressCalc('8')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">8</button>
                <button type="button" @click="pressCalc('9')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">9</button>
                <button type="button" @click="pressCalc('*')" class="h-9 rounded-xl text-blue-600 dark:text-[#93F514] font-black text-sm hover:bg-blue-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center" :class="calcExpr.trimEnd().endsWith('×') ? 'ring-2 ring-emerald-500 bg-blue-100 dark:bg-emerald-950/60' : 'bg-blue-50 dark:bg-[#14203A]'">×</button>

                <button type="button" @click="pressCalc('4')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">4</button>
                <button type="button" @click="pressCalc('5')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">5</button>
                <button type="button" @click="pressCalc('6')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">6</button>
                <button type="button" @click="pressCalc('-')" class="h-9 rounded-xl text-blue-600 dark:text-[#93F514] font-black text-sm hover:bg-blue-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center" :class="calcExpr.trimEnd().endsWith('−') ? 'ring-2 ring-emerald-500 bg-blue-100 dark:bg-emerald-950/60' : 'bg-blue-50 dark:bg-[#14203A]'">−</button>

                <button type="button" @click="pressCalc('1')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">1</button>
                <button type="button" @click="pressCalc('2')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">2</button>
                <button type="button" @click="pressCalc('3')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">3</button>
                <button type="button" @click="pressCalc('+')" class="h-9 rounded-xl text-blue-600 dark:text-[#93F514] font-black text-sm hover:bg-blue-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center" :class="calcExpr.trimEnd().endsWith('+') ? 'ring-2 ring-emerald-500 bg-blue-100 dark:bg-emerald-950/60' : 'bg-blue-50 dark:bg-[#14203A]'">+</button>

                <button type="button" @click="pressCalc('0')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">0</button>
                <button type="button" @click="pressCalc(',')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-800 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">,</button>
                <button type="button" @click="pressCalc('%')" class="h-9 rounded-xl bg-slate-50 dark:bg-[#14203A] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer flex items-center justify-center">%</button>
                <button type="button" @click="pressCalc('=')" class="h-9 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white dark:bg-[#93F514] dark:hover:bg-[#82dc12] dark:text-black font-black text-sm shadow-sm transition cursor-pointer flex items-center justify-center">=</button>
            </div>

            <!-- Footer Hint Keyboard & Numpad -->
            <div class="text-[9px] text-slate-400 dark:text-slate-500 text-center font-medium flex items-center justify-center gap-1.5 pt-0.5 shrink-0">
                <svg class="w-3 h-3 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Dukung input langsung dari <strong>Keyboard</strong> & <strong>Numpad</strong></span>
            </div>

            <!-- Corner Resize Handle (Tahan & Seret Sudut untuk Mengubah Ukuran) -->
            <div @mousedown.stop="startResize($event, 'calc')"
                 @touchstart.stop.passive="false"
                 @touchstart.stop="startResize($event, 'calc')"
                 @dblclick.stop="resetSize('calc')"
                 style="touch-action: none;"
                 class="absolute bottom-1 right-1 w-4 h-4 cursor-se-resize flex items-center justify-center text-amber-400 hover:text-amber-300 transition select-none z-20"
                 title="Tahan & seret sudut ini untuk memperbesar/memperkecil kalkulator (Klik 2x untuk reset)">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 19L11 19M19 19L19 11M19 14L14 19" />
                </svg>
            </div>
        </div>

        <!-- FLOATING WINDOW: LEMBAR CATATAN / SCRATCHPAD (BISA DISERET & DIATUR UKURAN) -->
        <div data-floating-tool="notes"
             x-show="showNotes"
             x-cloak
             @mousedown="bringToFront('notes')"
             :style="'position: fixed; top: ' + winPos.notes.y + 'px; left: ' + winPos.notes.x + 'px; width: ' + winSize.notes.w + 'px; height: ' + winSize.notes.h + 'px; z-index: ' + toolZ('notes') + '; ' + (showNotes ? 'display: flex;' : 'display: none !important;')"
             style="position: fixed; display: none;"
             class="max-w-[96vw] max-h-[92vh] bg-white dark:bg-gray-800 border-2 border-indigo-500/80 dark:border-indigo-500/60 rounded-2xl shadow-2xl p-4 space-y-3 flex-col overflow-hidden select-none relative">
            <!-- Header (Drag Handle) -->
            <div @mousedown="startDrag($event, 'notes')"
                 @touchstart="startDrag($event, 'notes')"
                 class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700 cursor-grab active:cursor-grabbing select-none">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    <span class="text-xs font-bold text-gray-900 dark:text-white">Lembar Catatan Ujian</span>
                    <span class="text-[10px] text-indigo-500 font-mono bg-indigo-50 dark:bg-indigo-950/40 px-1.5 py-0.5 rounded font-semibold">Tahan & Seret</span>
                </div>
                <div class="flex items-center gap-2" @mousedown.stop>
                    <button type="button" @click="insertQuestionNote()" class="px-2 py-1 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 transition cursor-pointer" title="Sisipkan tanda catatan">
                        + Catatan Soal
                    </button>
                    <button type="button" @click="closeTool('notes')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-white rounded-lg cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            <!-- Notes Textarea -->
            <textarea x-model="notesText" @input="saveNotes()" rows="8" 
                class="w-full flex-1 min-h-[130px] p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 text-xs font-mono text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 placeholder-gray-400 leading-relaxed resize-none"
                placeholder="Tulis coretan rumus, catatan, atau perhitungan di sini... Tersimpan otomatis di browser."></textarea>

            <div class="flex items-center justify-between text-[10px] text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>Tersimpan otomatis</span>
                </span>
                <button type="button" @click="clearNotes()" class="text-rose-500 hover:underline font-semibold flex items-center gap-1 cursor-pointer transition text-[11px]" :class="confirmClearNotes ? 'text-rose-600 font-extrabold animate-pulse' : ''">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span x-text="confirmClearNotes ? 'Yakin Hapus? Klik Sekali Lagi' : 'Bersihkan Catatan'">Bersihkan Catatan</span>
                </button>
            </div>

            <!-- Corner Resize Handle (Tahan & Seret Sudut untuk Mengubah Ukuran) -->
            <div @mousedown.stop="startResize($event, 'notes')"
                 @touchstart.stop="startResize($event, 'notes')"
                 @dblclick.stop="resetSize('notes')"
                 style="touch-action: none;"
                 class="absolute bottom-1 right-1 w-4 h-4 cursor-se-resize flex items-center justify-center text-indigo-400 hover:text-indigo-300 transition select-none z-20"
                 title="Tahan & seret sudut ini untuk memperbesar/memperkecil catatan (Klik 2x untuk reset)">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 19L11 19M19 19L19 11M19 14L14 19" />
                </svg>
            </div>
        </div>
        </div>

        {{-- ========================================================
             MODAL PENGAMAN ANTI-CONTEK & ANTI-SCREENSHOT
             ======================================================== --}}
        <!-- ANTI-SCREENSHOT & SNIPPING PRIVACY SHIELD -->
        <div x-show="isScreenObfuscated && !isGuarded && testState === 'taking'" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 z-[99998] bg-gray-950/95 backdrop-blur-[35px] flex items-center justify-center p-4 select-none">
            <div @click.stop class="max-w-md w-full bg-gray-900 border-2 border-rose-500/80 rounded-3xl p-6 sm:p-7 text-center space-y-4 shadow-2xl shadow-rose-950/60 cursor-default">
                <div class="w-16 h-16 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center mx-auto shadow-inner animate-pulse">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </div>
                <div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        Pengaman Anti-Screenshot CBT
                    </span>
                    <h3 class="text-lg font-black text-white mt-2">Layar Asesmen Disembunyikan</h3>
                    <p class="text-xs text-gray-300 mt-2 leading-relaxed">
                        Terdeteksi aktivitas tangkapan layar (Print Screen / Snipping Tool). Seluruh tampilan soal diburamkan dan aktivitas ini dicatat sebagai pelanggaran demi integritas keamanan.
                    </p>
                </div>
                <button type="button" @click="returnToFullscreen()" class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-md cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <span>Klik untuk Kembali ke Layar Asesmen</span>
                </button>
            </div>
        </div>

        <!-- PROCTORING / ANTI-CURANG FULLSCREEN GUARD OVERLAY -->
        <div x-show="isGuarded && testState === 'taking'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-cloak
             class="fixed inset-0 z-[99999] bg-gray-950/90 backdrop-blur-[24px] flex items-center justify-center p-4 cursor-pointer select-none">
            <div @click.stop class="max-w-md w-full bg-gray-900 border-2 border-rose-600/80 rounded-3xl p-6 sm:p-7 text-center space-y-5 shadow-2xl shadow-rose-950/60 cursor-default">
                <div class="w-16 h-16 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                          :class="violations === 0 ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30'"
                          x-text="violations === 0 ? 'Sistem CBT Karyawan Terpantau' : 'Peringatan Sistem Ujian Ketat'">
                    </span>
                    <h3 class="text-lg font-black text-white mt-2"
                        x-text="violations === 0 ? 'Mode Layar Penuh Diperlukan' : 'Aktivitas di Luar Jendela Terdeteksi!'"></h3>
                    <p class="text-xs font-medium mt-1 p-2.5 rounded-xl border"
                       :class="violations === 0 ? 'text-indigo-300 bg-indigo-950/60 border-indigo-900/60' : 'text-rose-300 bg-rose-950/60 border-rose-900/60'"
                       x-text="violationReason">
                        Fokus kursor berpindah ke luar jendela ujian
                    </p>
                </div>

                <!-- Strike Indicators -->
                <div class="p-3.5 rounded-2xl bg-gray-800 border border-gray-700 space-y-2">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="text-gray-400">Peringatan Pelanggaran:</span>
                        <span class="font-mono font-bold" :class="violations === 0 ? 'text-indigo-400' : 'text-rose-400'" x-text="Math.min(violations, maxViolations) + ' / ' + maxViolations + ' Pelanggaran'">1 / 3</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <div class="h-2 rounded-full transition-colors duration-300" :class="violations >= 1 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-gray-700'"></div>
                        <div class="h-2 rounded-full transition-colors duration-300" :class="violations >= 2 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-gray-700'"></div>
                        <div class="h-2 rounded-full transition-colors duration-300" :class="violations >= 3 ? 'bg-rose-500 shadow-sm shadow-rose-500/50' : 'bg-gray-700'"></div>
                    </div>
                </div>

                <p class="text-xs text-gray-400 leading-relaxed text-left">
                    Asesmen ini dipantau secara ketat. Anda <strong>wajib berada di mode Fullscreen</strong> sepanjang sesi. Dilarang menekan tombol ESC, berpindah tab/aplikasi lain, atau mengambil tangkapan layar (PrintScreen/Snipping Tool).
                    Jika mencapai <strong>3 kali pelanggaran</strong>, sistem akan langsung menghentikan asesmen dan <strong>mengumpulkan jawaban secara otomatis</strong>.
                </p>

                <template x-if="violations < maxViolations && !isAutoSubmitting">
                    <button type="button" @click="returnToFullscreen()"
                        class="w-full py-3 px-5 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-lg flex items-center justify-center gap-2 cursor-pointer"
                        :class="violations === 0 ? 'bg-indigo-600 hover:bg-indigo-500 shadow-indigo-600/30' : 'bg-rose-600 hover:bg-rose-500 shadow-rose-600/30'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                        <span x-text="violations === 0 ? 'Lanjutkan Masuk Layar Penuh' : 'Saya Mengerti & Kembali ke Layar Penuh'"></span>
                    </button>
                </template>

                <template x-if="violations >= maxViolations || isAutoSubmitting">
                    <div class="space-y-3">
                        <div class="p-3.5 rounded-xl bg-rose-950/80 border border-rose-800 text-xs text-rose-200 font-bold flex items-center justify-center gap-3">
                            <svg class="animate-spin w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Batas toleransi habis (3/3). Mengumpulkan jawaban otomatis...</span>
                        </div>
                        <p class="text-[11px] text-gray-400">
                            Sistem mendiskualifikasi sesi karena pelanggaran integritas dan memproses pengumpulan seluruh lembar jawaban Anda...
                        </p>
                    </div>
                </template>
            </div>
        </div>
    @endif

    <!-- STATE 3: COMPLETED / HASIL UJIAN -->
    @if ($testState === 'completed')
        @php
            $hasEssayQuestions = collect($questions)->contains('question_type', 'essay');
            $hasMultipleChoice = collect($questions)->contains('question_type', 'multiple_choice');
            $isWaitingReview = $hasEssayQuestions && ($attempt && is_null($attempt->essay_score));
        @endphp

        <div
            class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-10 text-center space-y-6">
            <div
                class="w-16 h-16 rounded-3xl {{ $isWaitingReview ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400' }} flex items-center justify-center mx-auto shadow-md">
                @if ($isWaitingReview)
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">
                    {{ $isWaitingReview ? 'Jawaban Asesmen Berhasil Terkirim!' : 'Asesmen Berhasil Diselesaikan!' }}
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Terima kasih telah menyelesaikan paket asesmen <span
                        class="font-bold text-gray-800 dark:text-gray-200">{{ $test->title }}</span>.
                </p>
            </div>

            @if ($isWaitingReview)
                <!-- Banner Khusus Menunggu Review Essay -->
                <div
                    class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-left space-y-2">
                    <div class="flex items-center gap-2 text-amber-800 dark:text-amber-200 font-bold text-xs">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Menunggu Evaluasi & Penilaian dari Penguji / HR</span>
                    </div>
                    <p class="text-[11px] text-amber-700 dark:text-amber-300 leading-relaxed">
                        Asesmen ini memuat soal berbentuk <strong>Uraian / Essay</strong>. Jawaban Anda telah tersimpan
                        dengan aman dan saat ini sedang dalam antrean pemeriksaan oleh tim penilai.
                    </p>
                </div>
            @endif

            @if ($discResult || str_contains(strtolower($test->category?->name ?? ''), 'disc'))
                <!-- Khusus Tes DISC: Sembunyikan detail hasil dari employee, hanya notifikasi tersimpan -->
                <div
                    class="p-6 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-center space-y-2">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 mb-1">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jawaban Anda Telah Tersimpan</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        Terima kasih telah menyelesaikan inventori kepribadian DISC. Seluruh jawaban Anda telah
                        tersimpan dengan aman dan akan dievaluasi langsung oleh Tim HR / Tim Penilai.
                    </p>
                </div>
            @elseif ($papiResult || str_contains(strtolower($test->category?->name ?? ''), 'papi'))

                <!-- Tes PAPI Kostick: Tampilkan hanya notifikasi tersimpan -->
                <div class="p-6 rounded-2xl bg-indigo-50/50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-800/60 text-center space-y-2">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 mb-1">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jawaban Anda Telah Tersimpan</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto leading-relaxed">
                        Terima kasih telah menyelesaikan inventori PAPI Kostick. Seluruh jawaban Anda telah
                        tersimpan dengan aman dan akan dievaluasi langsung oleh Tim HR / Tim Penilai.
                    </p>
                </div>
            @else
                <!-- Score Summary Card untuk Tes Non-DISC (Pilihan Ganda & Essay) -->
                <div
                    class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 space-y-3">
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                        <span>Status Pengerjaan:</span>
                        @if ($isWaitingReview)
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                Menunggu Review Essay
                            </span>
                        @elseif ($attempt && $attempt->status === 'passed')
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Lulus (Passed)
                            </span>
                        @elseif ($attempt && $attempt->status === 'failed')
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                Belum Lolos KKM
                            </span>
                        @else
                            <span
                                class="font-bold px-3 py-1 rounded-full text-[11px] bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Selesai (Completed)
                            </span>
                        @endif
                    </div>

                    @if ($hasMultipleChoice)
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span>Skor Pilihan Ganda:</span>
                            <span class="font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">
                                {{ number_format($attempt->objective_score ?? 0, 1) }} Poin
                            </span>
                        </div>
                    @endif

                    @if ($hasEssayQuestions)
                        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                            <span>Skor Essay / Uraian:</span>
                            @if ($attempt && $attempt->essay_score !== null)
                                <span class="font-extrabold text-amber-600 dark:text-amber-400 text-sm">
                                    {{ number_format($attempt->essay_score, 1) }} Poin
                                </span>
                            @else
                                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 italic">
                                    Sedang Dinilai Tim HR
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="font-bold text-xs text-gray-900 dark:text-white">Total Skor Akhir:</span>
                        @if ($isWaitingReview)
                            <span
                                class="text-xs font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-200 dark:border-amber-800">
                                Menunggu Penilaian Essay
                            </span>
                        @else
                            <span class="text-xl font-black text-gray-900 dark:text-white">
                                {{ number_format($attempt->total_score ?? 0, 1) }}
                            </span>
                        @endif
                    </div>

                    @if ($attempt)
                        {{-- <div class="pt-3 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-center gap-2.5">
                            <a href="{{ route('test_evaluation.result_pdf', $attempt->id) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-xl text-xs font-semibold shadow-2xs transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <span>Preview PDF Hasil Asesmen</span>
                            </a>
                            <a href="{{ route('test_evaluation.result_pdf', ['id' => $attempt->id, 'download' => 1]) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-sm shadow-indigo-500/20 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh PDF</span>
                            </a>
                        </div> --}}
                    @endif
                </div>
            @endif


            <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed pt-4">
                Hasil asesmen ini telah tersimpan dalam rekam jejak kompetensi karyawan.
            </p>

            <div class="pt-4">
                <a href="{{ route('profile') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-500/20 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Dashboard Asesmen</span>
                </a>
            </div>
        </div>
    @endif

    {{-- ==========================================
         PROTEKSI ANTI-COPY & PRINT SOAL ASESMEN
         ========================================== --}}
    <style>
    @media print {
        html, body {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    }

    #soal-area .soal-teks,
    #soal-area .soal-opsi,
    #soal-area .soal-opsi label,
    #soal-area .soal-opsi span {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }

    #soal-area .soal-teks,
    #soal-area .soal-opsi {
        -webkit-user-drag: none;
        pointer-events: auto;
    }

    #soal-area .soal-teks::selection,
    #soal-area .soal-opsi *::selection {
        background: transparent !important;
    }
    #soal-area .soal-teks::-moz-selection,
    #soal-area .soal-opsi *::-moz-selection {
        background: transparent !important;
    }
    </style>

    <script>
    function initCopyProtection() {
        if (window._blockCopyHandler) {
            document.removeEventListener('copy', window._blockCopyHandler, true);
            document.removeEventListener('cut', window._blockCopyHandler, true);
            document.removeEventListener('contextmenu', window._blockContextHandler, true);
            document.removeEventListener('keydown', window._blockKeyHandler, true);
        }
        function blockCopy(e) {
            const soalArea = document.getElementById('soal-area');
            if (!soalArea) return;

            let target = e.target;
            let isInsideSoal = soalArea.contains(target);

            if (!isInsideSoal) {
                const sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    const range = sel.getRangeAt(0);
                    const ancestor = range.commonAncestorContainer;
                    isInsideSoal = soalArea.contains(
                        ancestor.nodeType === 3 ? ancestor.parentElement : ancestor
                    );
                }
            }

            if (isInsideSoal) {
                e.preventDefault();
                e.stopPropagation();
                try {
                    if (e.clipboardData) e.clipboardData.setData('text/plain', '');
                    if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText('').catch(() => {});
                } catch(err) {}

                showCopyBlockedToast('Penyalinan soal tidak diperbolehkan!');
                return false;
            }
        }

        document.removeEventListener('copy', window._blockCopyHandler, true);
        document.removeEventListener('cut', window._blockCopyHandler, true);
        document.removeEventListener('contextmenu', window._blockContextHandler, true);
        document.removeEventListener('keydown', window._blockKeyHandler, true);

        function blockKey(e) {
            const soalArea = document.getElementById('soal-area');
            if (!soalArea) return;
            const isCopy = (e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C');
            const isSelectAll = (e.ctrlKey || e.metaKey) && (e.key === 'a' || e.key === 'A');
            const isCut = (e.ctrlKey || e.metaKey) && (e.key === 'x' || e.key === 'X');

            if (isCopy || isSelectAll || isCut) {
                const sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    const range = sel.getRangeAt(0);
                    const ancestor = range.commonAncestorContainer;
                    const el = ancestor.nodeType === 3 ? ancestor.parentElement : ancestor;
                    if (soalArea.contains(el)) {
                        e.preventDefault();
                        e.stopPropagation();
                        try {
                            if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText('').catch(() => {});
                        } catch(err) {}
                        showCopyBlockedToast('Penyalinan teks dilarang demi integritas ujian!');
                        window.getSelection().removeAllRanges();
                        return false;
                    }
                }
            }
        }

        window._blockCopyHandler = blockCopy;
        window._blockContextHandler = blockCopy;
        window._blockKeyHandler = blockKey;

        document.addEventListener('copy', blockCopy, true);
        document.addEventListener('cut', blockCopy, true);
        document.addEventListener('contextmenu', blockCopy, true);
        document.addEventListener('keydown', blockKey, true);
    }

    let _copyToastTimeout = null;
    function showCopyBlockedToast(customMessage) {
        let msg = customMessage || 'Penyalinan soal tidak diperbolehkan.';
        let toast = document.getElementById('copy-blocked-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'copy-blocked-toast';
            toast.style.cssText = `
                position: fixed;
                bottom: 24px;
                left: 50%;
                transform: translateX(-50%) translateY(80px);
                background: #0f172a;
                color: #f1f5f9;
                padding: 10px 18px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 10px 35px rgba(0,0,0,0.45);
                z-index: 99999;
                transition: transform 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s ease;
                opacity: 0;
                border: 1px solid rgba(244,63,94,0.4);
                white-space: nowrap;
            `;
            document.body.appendChild(toast);
        }

        toast.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
            <span>${msg}</span>
        `;

        clearTimeout(_copyToastTimeout);
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(-50%) translateY(0)';
            toast.style.opacity = '1';
        });

        _copyToastTimeout = setTimeout(() => {
            toast.style.transform = 'translateX(-50%) translateY(80px)';
            toast.style.opacity = '0';
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if ($testState === 'taking')
            initCopyProtection();
        @endif
    });
    document.addEventListener('livewire:load', function() {
        @if ($testState === 'taking')
            initCopyProtection();
        @endif
    });
    document.addEventListener('livewire:update', function() {
        @if ($testState === 'taking')
            initCopyProtection();
        @endif
    });
    </script>
</div>
