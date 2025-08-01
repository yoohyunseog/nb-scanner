class NBScanner {
    constructor() {
        this.stage = 0;
        this.isRunning = false;
        this.intervalId = null;
        this.scanInterval = 1000;

        // 스캔할 태그 목록 (우선순위 순)
        this.targetTags = ['H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'P', 'DIV', 'SPAN', 'LI', 'TD', 'TH'];
        this.currentTagIndex = 0;
        this.currentElementIndex = 0;

        // 스캔 스타일
        this.scanStyle = {
            backgroundColor: '#fff3cd',
            transition: 'background-color 0.3s'
        };
        
        // 단어 저장소
        this.words = new Map();
        
        // 진행 상태
        this.progress = {
            total: 0,
            current: 0
        };

        this.lastHighlightedElement = null;

        // UI 요소
        this.ui = {
            container: document.getElementById('nbScannerContainer'),
            progress: document.querySelector('.progress-fill'),
            status: document.querySelector('.scan-status'),
            count: document.querySelector('.scan-count'),
            speedControl: document.querySelector('.scan-speed'),
            startButton: document.querySelector('.start-scan'),
            pauseButton: document.querySelector('.pause-scan')
        };

        // UI 이벤트 리스너 설정
        this.initializeUI();
    }

    initializeUI() {
        if (this.ui.startButton) {
            this.ui.startButton.addEventListener('click', () => this.start());
        }
        if (this.ui.pauseButton) {
            this.ui.pauseButton.addEventListener('click', () => this.stop());
        }
        if (this.ui.speedControl) {
            this.ui.speedControl.addEventListener('change', (e) => {
                this.setScanSpeed(parseInt(e.target.value));
            });
        }
    }

    updateUI() {
        const currentTag = this.targetTags[this.currentTagIndex];
        const elements = document.getElementsByTagName(currentTag);
        const progress = (this.currentElementIndex / elements.length) * 100;

        if (this.ui.progress) {
            this.ui.progress.style.width = `${Math.min(progress, 100)}%`;
        }
        if (this.ui.status) {
            this.ui.status.textContent = `스캔 중: ${currentTag} 태그`;
        }
        if (this.ui.count) {
            this.ui.count.textContent = `${this.currentElementIndex} / ${elements.length}`;
        }
    }

    scanWords() {
        switch (this.stage) {
            case 0:  // 초기화
                console.log("Stage 0: 스캔 초기화");
                this.currentTagIndex = 0;
                this.currentElementIndex = 0;
                this.stage = 1;
                this.updateUI(); // Added updateUI for stage 0
                break;

            case 1:  // 태그별 순차 스캔
                const currentTag = this.targetTags[this.currentTagIndex];
                const elements = document.getElementsByTagName(currentTag);
                
                // 이전 하이라이트 제거
                if (this.lastHighlightedElement) {
                    Object.keys(this.scanStyle).forEach(style => {
                        this.lastHighlightedElement.style[style] = '';
                    });
                }

                if (this.currentElementIndex < elements.length) {
                    const element = elements[this.currentElementIndex];
                    
                    // 현재 요소 하이라이트
                    Object.keys(this.scanStyle).forEach(style => {
                        element.style[style] = this.scanStyle[style];
                    });
                    this.lastHighlightedElement = element;

                    // 단어 추출
                    const text = element.textContent.trim();
                    if (text) {
                        const words = text.match(/[\uAC00-\uD7AF\u1100-\u11FF\u3130-\u318F]{2,}/g) || [];
                        words.forEach(word => {
                            if (!this.words.has(word)) {
                                this.words.set(word, {
                                    count: 1,
                                    elements: [element],
                                    tag: currentTag
                                });
                            } else {
                                const info = this.words.get(word);
                                info.count++;
                                info.elements.push(element);
                            }
                        });
                    }

                    this.currentElementIndex++;
                    console.log(`스캔 중: ${currentTag} 태그 (${this.currentElementIndex}/${elements.length})`);
                    this.updateUI(); // Added updateUI for stage 1
                } else {
                    // 현재 태그의 모든 요소를 처리했으면 다음 태그로
                    this.currentTagIndex++;
                    this.currentElementIndex = 0;
                    
                    if (this.currentTagIndex >= this.targetTags.length) {
                        this.stage = 2;
                    }
                    this.updateUI(); // Added updateUI for stage 1
                }
                break;

            case 2:  // 스캔 완료
                console.log("스캔 완료:", this.words);
                // 마지막 하이라이트 제거
                if (this.lastHighlightedElement) {
                    Object.keys(this.scanStyle).forEach(style => {
                        this.lastHighlightedElement.style[style] = '';
                    });
                }
                this.stop();
                if (this.ui.status) {
                    this.ui.status.textContent = "스캔 완료";
                }
                if (this.ui.progress) {
                    this.ui.progress.style.width = "100%";
                }
                break;
        }
    }

    start() {
        if (this.isRunning) return;
        this.isRunning = true;
        console.log("스캔 시작");
        
        this.intervalId = setInterval(() => {
            this.scanWords();
        }, this.scanInterval);
    }

    stop() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
        this.isRunning = false;
        console.log("스캔 중지");
    }

    setScanSpeed(speed) {
        this.scanInterval = speed;
        if (this.isRunning) {
            clearInterval(this.intervalId);
            this.intervalId = setInterval(() => {
                this.scanWords();
            }, this.scanInterval);
        }
    }
}

// 전역 스캐너 인스턴스 생성
window.nbScanner = new NBScanner();