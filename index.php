<?php
if (!defined('_GNUBOARD_')) exit;
$version = time();
?>

<!-- HTML 구조 -->
<div id="nbScannerContainer" class="nb-scanner-container">
    <div class="toggle-button">
        <div class="icon"></div>
        <span class="text">열기</span>
    </div>
    
    <div class="scan-results">
        <div class="results-header">
            <h5>발견된 단어</h5>
            <span class="word-total">총 0개</span>
        </div>
        <div class="word-list"></div>
    </div>

    <div class="nb-scanner-progress">
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <div class="progress-info">
            <span id="nb-status" class="scan-status">스캔 준비중...</span>
            <span id="nb-word-count" class="scan-count">0 / 100</span>
        </div>
    </div>

    <div class="nb-scanner-controls d-none">
        <button id="nb-toggle-btn" class="btn btn-primary">시작</button>
        <button id="nb-pause-btn" class="btn btn-warning" disabled>일시정지</button>
    </div>
</div>

<style>
.nb-scanner-container {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    width: 300px;
    background: #ffffff4a;
    border-radius: 0 0 0 15px;
    box-shadow: -2px 0 20px rgba(107, 111, 213, 0.15);
    padding: 20px;
    z-index: 1000;
    transition: all 0.3s ease;
}

.nb-scanner-container.minimized {
    right: -280px;
}

.toggle-button {
    position: absolute;
    left: -32px;
    top: 50%;
    transform: translateY(-50%);
    width: 32px;
    height: 80px;
    background: #6b6fd5;
    border-radius: 6px 0 0 6px;
    box-shadow: -2px 0 10px rgba(107, 111, 213, 0.2);
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 0;
    transition: all 0.3s ease;
}

.toggle-button:hover {
    background: #5a5ec4;
}

.toggle-button .icon {
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M9.29 6.71a.996.996 0 0 0 0 1.41L13.17 12l-3.88 3.88a.996.996 0 1 0 1.41 1.41l4.59-4.59a.996.996 0 0 0 0-1.41L10.7 6.7c-.38-.38-1.02-.38-1.41.01z'/%3E%3C/svg%3E");
    background-size: contain;
    background-repeat: no-repeat;
    transition: transform 0.3s ease;
}

.toggle-button .text {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    font-size: 12px;
    color: white;
    letter-spacing: 1px;
}

.nb-scanner-container.minimized .toggle-button .icon {
    transform: rotate(180deg);
}

.scan-results {
    margin-bottom: 15px;
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    border-bottom: 2px solid rgba(107, 111, 213, 0.1);
    padding-bottom: 10px;
}

.results-header h5 {
    margin: 0;
    font-size: 14px;
    color: #6b6fd5;
    font-weight: 600;
}

.word-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
    background: rgba(107, 111, 213, 0.05);
    border-radius: 10px;
    margin-bottom: 15px;
    scrollbar-width: thin;
}

.word-list::-webkit-scrollbar {
    width: 4px;
}

.word-list::-webkit-scrollbar-track {
    background: rgba(107, 111, 213, 0.05);
}

.word-list::-webkit-scrollbar-thumb {
    background: rgba(107, 111, 213, 0.3);
    border-radius: 2px;
}

.word-item {
    background: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(107, 111, 213, 0.1);
    border: 1px solid rgba(107, 111, 213, 0.1);
    color: #6b6fd5;
}

.word-item:hover {
    background: #6b6fd5;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(107, 111, 213, 0.2);
}

.progress-bar {
    background: rgba(107, 111, 213, 0.1);
    height: 6px;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    background: linear-gradient(90deg, #6b6fd5, #5a5ec4);
    height: 100%;
    width: 0;
    transition: width 0.3s ease;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
    font-size: 12px;
    color: #6b6fd5;
}

@keyframes wordPulse {
    0% { 
        background-color: transparent;
        transform: scale(1);
    }
    50% { 
        background-color: rgba(107, 111, 213, 0.1);
        transform: scale(1.02);
    }
    100% { 
        background-color: transparent;
        transform: scale(1);
    }
}

/* 기존 CSS 애니메이션 제거 - setInterval로 대체 */

.word-found-animation {
    animation: wordPulse 2s ease infinite;
    position: relative;
    z-index: 1000;
    box-shadow: 0 0 10px rgba(107, 111, 213, 0.2);
}

.word-highlight {
    color: #000 !important;
    padding: 2px 4px;
    border-radius: 3px;
    font-weight: bold;
    position: relative;
    z-index: 1000;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    transition: all 0.1s ease;
}

.sentence-highlight {
    border: 3px solid transparent !important;
    border-radius: 8px;
    padding: 8px;
    position: relative;
    z-index: 1000;
    backdrop-filter: blur(5px);
    transition: all 0.1s ease;
}

.d-none {
    display: none !important;
}
</style>

<script>
// NBScanner 클래스 정의
class NBScanner {
    constructor() {
        try {
            console.log('NBScanner 초기화 시작');
            
            // 초기화 전에 DOM 요소 존재 확인
            if (!this.checkRequiredElements()) {
                throw new Error('필수 DOM 요소가 없습니다.');
            }

            // 상태 초기화
            this.initializeState();
            // UI 요소 초기화
            this.initializeUI();
            // 토글 버튼 초기화
            this.initializeToggle();
            // 전체 페이지 요소 초기화
            this.initializePageScan();

            // 즉시 시작
            setTimeout(() => {
                this.start();
            }, 1000);
            
            console.log('NBScanner 초기화 완료');
        } catch (error) {
            console.error('NBScanner 초기화 오류:', error);
        }
    }

    checkRequiredElements() {
        const container = document.getElementById('nbScannerContainer');
        if (!container) {
            console.error('nbScannerContainer를 찾을 수 없습니다.');
            return false;
        }

        const required = [
            '.word-list',
            '.word-total',
            '#nb-status',
            '.progress-fill',
            '#nb-word-count'
        ];

        for (const selector of required) {
            if (!container.querySelector(selector)) {
                console.error(`필수 요소를 찾을 수 없습니다: ${selector}`);
                return false;
            }
        }
        
        console.log('모든 필수 DOM 요소 확인 완료');
        return true;
    }

    initializeState() {
        this.isRunning = false;
        this.intervalId = null;
        this.progress = 0;
        this.currentIndex = 0;
        this.processedWords = new Set();
        this.lastScannedElement = null;
        
        // 스캔 설정
        this.scanSettings = {
            maxElements: 500, // 최대 스캔할 요소 개수 (0 = 제한 없음)
            minTextLength: 2, // 최소 텍스트 길이
            scanInterval: 100 // 스캔 간격 (ms)
        };
        
        console.log('상태 초기화 완료');
    }

    initializeUI() {
        const container = document.getElementById('nbScannerContainer');
        
        // UI 요소 참조 저장
        this.elements = {
            wordList: container.querySelector('.word-list'),
            wordTotal: container.querySelector('.word-total'),
            status: container.querySelector('#nb-status'),
            progressBar: container.querySelector('.progress-fill'),
            wordCount: container.querySelector('#nb-word-count')
        };

        // 초기 상태 설정
        this.elements.status.textContent = '스캔 준비중...';
        this.elements.wordCount.textContent = '0 / 100';
        this.elements.wordTotal.textContent = '총 0개';
        console.log('UI 초기화 완료');
    }

    initializeToggle() {
        try {
            const container = document.getElementById('nbScannerContainer');
            const toggleButton = container.querySelector('.toggle-button');
            
            // 토글 기능
            toggleButton.addEventListener('click', (e) => {
                e.stopPropagation();
                container.classList.toggle('minimized');
                toggleButton.querySelector('.text').textContent = 
                    container.classList.contains('minimized') ? '열기' : '닫기';
            });

            // 외부 클릭 시 최소화
            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    container.classList.add('minimized');
                    toggleButton.querySelector('.text').textContent = '열기';
                }
            });

            // 내부 클릭 이벤트 전파 방지
            container.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // 초기 상태 설정
            container.classList.add('minimized');
            toggleButton.querySelector('.text').textContent = '열기';
            console.log('토글 버튼 초기화 완료');

        } catch (error) {
            console.error('토글 초기화 오류:', error);
        }
    }

    initializePageScan() {
        // 전체 페이지의 모든 요소를 가져옴
        this.allElements = [];
        this.currentIndex = 0;

        console.log('=== 페이지 구조 진단 시작 ===');
        console.log('현재 페이지 URL:', window.location.href);
        console.log('페이지 제목:', document.title);
        
        // 페이지의 모든 제목 태그와 P 태그 확인
        const targetTags = ['H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'P'];
        let totalElements = 0;
        
        targetTags.forEach(tag => {
            const elements = document.getElementsByTagName(tag);
            totalElements += elements.length;
            console.log(`전체 페이지 ${tag} 태그: ${elements.length}개`);
            for (let i = 0; i < Math.min(elements.length, 3); i++) {
                console.log(`  ${tag} ${i+1}:`, elements[i].textContent.trim());
            }
        });
        
        console.log(`총 대상 태그 개수: ${totalElements}개`);

        // main 태그 찾기
        const mainElement = document.querySelector('main');
        let scanRoot = mainElement;
        
        if (!mainElement) {
            console.warn('main 태그를 찾을 수 없습니다. body 전체를 스캔합니다.');
            scanRoot = document.body;
        } else {
            console.log('main 태그 발견:', mainElement);
        }

        // 스캔 루트 내부의 대상 태그 확인
        let scanRootElements = 0;
        targetTags.forEach(tag => {
            const elements = scanRoot.getElementsByTagName(tag);
            scanRootElements += elements.length;
            console.log(`${scanRoot === mainElement ? 'main 내부' : 'body 내부'} ${tag} 태그: ${elements.length}개`);
        });
        
        console.log(`${scanRoot === mainElement ? 'main 내부' : 'body 내부'} 총 대상 태그: ${scanRootElements}개`);

        // TreeWalker를 사용하여 대상 요소 찾기
        const walker = document.createTreeWalker(
            scanRoot, // main 태그 또는 body를 루트로 설정
            NodeFilter.SHOW_ELEMENT,
            {
                acceptNode: (node) => {
                    // 제목 태그와 P 태그 허용
                    if (targetTags.includes(node.tagName)) {
                        // 텍스트가 있고, 스크립트나 스타일이 아닌 요소만
                        if (node.textContent && 
                            node.textContent.trim() && 
                            node.textContent.trim().length >= this.scanSettings.minTextLength &&
                            !node.closest('script') && 
                            !node.closest('style') &&
                            !node.closest('noscript')) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                    }
                    return NodeFilter.FILTER_REJECT;
                }
            }
        );

        let node;
        let elementCount = 0;
        while (node = walker.nextNode()) {
            // 최대 스캔 개수 제한 확인
            if (this.scanSettings.maxElements > 0 && elementCount >= this.scanSettings.maxElements) {
                console.log(`최대 스캔 개수(${this.scanSettings.maxElements}개)에 도달했습니다.`);
                break;
            }
            
            // 중복 제거를 위해 이미 추가된 요소가 아닌지 확인
            if (!this.allElements.includes(node)) {
                this.allElements.push(node);
                elementCount++;
                console.log('발견된 요소:', {
                    tag: node.tagName,
                    text: node.textContent.trim().substring(0, 30) + '...'
                });
            }
        }

        console.log(`스캔 대상: ${this.allElements.length}개 요소`);
        if (this.scanSettings.maxElements > 0) {
            console.log(`스캔 제한: 최대 ${this.scanSettings.maxElements}개`);
        }
        
        // 모든 제목 요소에 대해 NB 값을 선제적으로 계산
        this.precalculateNBsForAllElements();
        
                    // 스캔할 요소가 없으면 대체 스캔 시도
            if (this.allElements.length === 0) {
                console.warn('페이지에 대상 요소가 없습니다. 대체 스캔을 시도합니다.');
                this.tryAlternativeScan();
            }
    }

    precalculateNBsForAllElements() {
        console.log('=== 모든 대상 요소에 대해 NB 값 선제 계산 시작 ===');
        
        let calculatedCount = 0;
        let skippedCount = 0;
        
        this.allElements.forEach((element, index) => {
            try {
                const text = element.textContent;
                
                // NB 계산 함수 확인
                if (typeof wordNbUnicodeFormat === 'function' && 
                    typeof BIT_MAX_NB === 'function' && 
                    typeof BIT_MIN_NB === 'function') {
                    
                    const unicode = wordNbUnicodeFormat(text);
                    const elementBitMax = BIT_MAX_NB(unicode);
                    const elementBitMin = BIT_MIN_NB(unicode);
                    
                    // 제목 요소에 모든 NB 관련 속성 저장 (HTML에 직접 반영)
                    element.setAttribute('data-nb-max', elementBitMax);
                    element.setAttribute('data-nb-min', elementBitMin);
                    element.setAttribute('data-nb-text', text);
                    element.setAttribute('data-sentence-bit-max', elementBitMax);
                    element.setAttribute('data-sentence-bit-min', elementBitMin);
                    
                    // 툴팁으로 NB 값 표시
                    element.setAttribute('title', `[NB] max: ${elementBitMax}, min: ${elementBitMin}`);
                    
                    calculatedCount++;
                    
                    if (index < 5) { // 처음 5개만 로그 출력
                        console.log(`NB 계산 완료 [${index + 1}]:`, {
                            tag: element.tagName,
                            text: text.substring(0, 30) + '...',
                            bitMax: elementBitMax,
                            bitMin: elementBitMin
                        });
                    }
                } else {
                    console.warn('NB 계산 함수가 로드되지 않았습니다. 기본값 사용.');
                    // 기본값 설정
                    const elementBitMax = Math.random() * 5 + 1; // 1-6 범위
                    const elementBitMin = Math.random() * 3 + 1; // 1-4 범위
                    element.setAttribute('data-nb-max', elementBitMax);
                    element.setAttribute('data-nb-min', elementBitMin);
                    element.setAttribute('data-nb-text', text);
                    element.setAttribute('data-sentence-bit-max', elementBitMax);
                    element.setAttribute('data-sentence-bit-min', elementBitMin);
                    element.setAttribute('title', `[NB] max: ${elementBitMax}, min: ${elementBitMin}`);
                    
                    calculatedCount++;
                }
            } catch (error) {
                console.error(`요소 ${index} NB 계산 중 오류:`, error);
                skippedCount++;
            }
        });
        
        console.log(`=== NB 값 선제 계산 완료 ===`);
        console.log(`- 계산 완료: ${calculatedCount}개`);
        console.log(`- 계산 실패: ${skippedCount}개`);
        console.log(`- 총 대상 요소: ${this.allElements.length}개`);
    }

    highlightWordInElement(element, word) {
        try {
            const text = element.textContent;
            const wordIndex = text.indexOf(word);
            
            if (wordIndex !== -1) {
                // 단어를 span으로 감싸서 하이라이트
                const beforeWord = text.substring(0, wordIndex);
                const afterWord = text.substring(wordIndex + word.length);
                
                // 기존 내용을 span으로 교체
                element.innerHTML = `${beforeWord}<span class="word-highlight">${word}</span>${afterWord}`;
                
                // 무지개 색상 배열
                const rainbowColors = [
                    '#ffeb3b', '#ff9800', '#ff5722', '#e91e63', '#9c27b0', 
                    '#3f51b5', '#2196f3', '#00bcd4', '#009688', '#4caf50', 
                    '#8bc34a', '#cddc39'
                ];
                
                // 랜덤 색상 반짝임 효과 시작
                const wordElement = element.querySelector('.word-highlight');
                if (wordElement) {
                    this.startRainbowTwinkle(wordElement, rainbowColors, 100); // 100ms 간격
                }
                
                console.log('단어 하이라이트 적용:', {
                    word: word,
                    element: element.tagName,
                    position: wordIndex
                });
            } else {
                // 단어를 찾을 수 없는 경우 전체 요소에 하이라이트
                element.classList.add('word-found-animation');
                console.warn('단어를 찾을 수 없어 전체 요소에 하이라이트 적용:', word);
            }
        } catch (error) {
            console.error('단어 하이라이트 중 오류:', error);
            // 오류 발생 시 전체 요소에 하이라이트
            element.classList.add('word-found-animation');
        }
    }

    highlightSentenceInElement(element) {
        try {
            // 전체 문장에 하이라이트 적용
            element.classList.add('sentence-highlight');
            
            // 파란색 계열 색상 배열
            const blueColors = [
                '#e3f2fd', '#bbdefb', '#90caf9', '#64b5f6', '#42a5f5', 
                '#2196f3', '#1e88e5', '#1976d2', '#1565c0', '#0d47a1'
            ];
            
            // 랜덤 색상 반짝임 효과 시작
            this.startRainbowTwinkle(element, blueColors, 150); // 150ms 간격
            
            console.log('문장 하이라이트 적용:', {
                element: element.tagName,
                text: element.textContent.trim().substring(0, 30) + '...'
            });
        } catch (error) {
            console.error('문장 하이라이트 중 오류:', error);
        }
    }

    startRainbowTwinkle(element, colors, interval) {
        try {
            // 기존 interval이 있으면 중지
            if (element.twinkleInterval) {
                clearInterval(element.twinkleInterval);
            }
            
            // 랜덤 색상 반짝임 시작
            element.twinkleInterval = setInterval(() => {
                // 랜덤 색상 선택
                const randomColor = colors[Math.floor(Math.random() * colors.length)];
                
                // 배경색과 그림자 변경
                element.style.backgroundColor = randomColor;
                element.style.boxShadow = `0 0 15px ${randomColor}`;
                
                // 약간의 스케일 효과
                element.style.transform = `scale(${1 + Math.random() * 0.1})`;
                
            }, interval);
            
            console.log('랜덤 색상 반짝임 시작:', {
                element: element.tagName,
                colors: colors.length,
                interval: interval + 'ms'
            });
            
        } catch (error) {
            console.error('색상 반짝임 시작 중 오류:', error);
        }
    }

    clearAllAnimations() {
        try {
            // 모든 하이라이트 클래스 제거
            Array.from(document.querySelectorAll('.word-highlight')).forEach(el => {
                el.classList.remove('word-highlight');
                // 반짝임 interval 중지
                if (el.twinkleInterval) {
                    clearInterval(el.twinkleInterval);
                    el.twinkleInterval = null;
                }
                // 스타일 초기화
                el.style.backgroundColor = '';
                el.style.boxShadow = '';
                el.style.transform = '';
            });
            Array.from(document.querySelectorAll('.sentence-highlight')).forEach(el => {
                el.classList.remove('sentence-highlight');
                // 반짝임 interval 중지
                if (el.twinkleInterval) {
                    clearInterval(el.twinkleInterval);
                    el.twinkleInterval = null;
                }
                // 스타일 초기화
                el.style.backgroundColor = '';
                el.style.boxShadow = '';
                el.style.transform = '';
            });
            Array.from(document.querySelectorAll('.word-found-animation')).forEach(el => {
                el.classList.remove('word-found-animation');
            });
            
            // 애니메이션 강제 중지
            Array.from(document.querySelectorAll('*')).forEach(el => {
                if (el.style.animation) {
                    el.style.animation = 'none';
                    // 강제로 리플로우 후 애니메이션 제거
                    el.offsetHeight;
                    el.style.animation = '';
                }
            });
            
            console.log('모든 애니메이션 효과 종료');
        } catch (error) {
            console.error('애니메이션 제거 중 오류:', error);
        }
    }

    tryAlternativeScan() {
        console.log('=== 대체 스캔 방법 시도 ===');
        
        // 모든 H1 요소에서 단어 찾기
        const textElements = document.querySelectorAll('h1, a, p');
        console.log(`H1 요소 총 개수: ${textElements.length}개`);
        
        let potentialHeadings = [];
        textElements.forEach((element, index) => {
            const text = element.textContent.trim();
            console.log(`H1 요소 ${index + 1}:`, text);
            
            // 모든 H1 요소를 포함 (조건 없이)
            if (text && text.length > 0) {
                potentialHeadings.push({
                    element: element,
                    text: text,
                    tag: element.tagName
                });
            }
        });
        
        console.log(`잠재적 제목 요소: ${potentialHeadings.length}개`);
        potentialHeadings.slice(0, 10).forEach((item, index) => {
            console.log(`  잠재적 제목 ${index + 1}: [${item.tag}] ${item.text}`);
        });
        
        // 모든 H1 요소를 스캔 대상에 추가
        if (potentialHeadings.length > 0) {
            this.allElements = potentialHeadings.map(item => item.element); // 제한 없이 모든 요소 포함
            console.log(`대체 스캔 대상으로 ${this.allElements.length}개 H1 요소 설정`);
        }
    }

    scanNext() {
        try {
            if (this.currentIndex >= this.allElements.length) {
                this.complete();
                return;
            }

            // 진행률 계산
            this.progress = Math.min(
                (this.currentIndex / this.allElements.length) * 100,
                100
            );

            // 현재 인덱스의 요소 처리
            const element = this.allElements[this.currentIndex];
            
            // 이전 하이라이트 제거
            if (this.lastScannedElement) {
                this.lastScannedElement.style.backgroundColor = '';
            }

            // 현재 요소 하이라이트
            element.style.backgroundColor = '#fff3cd';
            this.lastScannedElement = element;

            // 상태 메시지 업데이트
            this.elements.status.textContent = `요소 스캔 중... (${element.tagName})`;
            this.elements.wordCount.textContent = 
                `${Math.round(this.progress)}% (${this.currentIndex + 1}/${this.allElements.length})`;

            // 텍스트 처리
            const text = element.textContent;
            console.log('현재 스캔:', {
                tag: element.tagName,
                text: text,
                index: this.currentIndex,
                total: this.allElements.length
            });

            // 이미 precalculateNBsForAllElements()에서 NB 값이 계산되어 저장됨
            const elementBitMax = parseFloat(element.dataset.nbMax) || 0;
            const elementBitMin = parseFloat(element.dataset.nbMin) || 0;
            
            console.log('현재 요소 NB 값:', {
                text: text,
                bitMax: elementBitMax,
                bitMin: elementBitMin
            });

            // 한글 단어 찾기 (2글자 이상)
            const words = text.match(/[가-힣]{2,}/g) || [];
            console.log('발견된 단어:', words);
            
            // 문장 자체로 NB 계산
            let sentenceBitMax = 0;
            let sentenceBitMin = 0;
            
            // 문장 전체 NB 계산
            if (typeof wordNbUnicodeFormat === 'function' && 
                typeof BIT_MAX_NB === 'function' && 
                typeof BIT_MIN_NB === 'function') {
                
                const unicode = wordNbUnicodeFormat(text);
                sentenceBitMax = BIT_MAX_NB(unicode);
                sentenceBitMin = BIT_MIN_NB(unicode);
                
                console.log('문장 NB 계산 완료:', text, sentenceBitMax, sentenceBitMin);
            } else {
                console.warn('NB 계산 함수가 로드되지 않았습니다. 기본값 사용.');
                // 기본값 설정
                sentenceBitMax = Math.random() * 5 + 1; // 1-6 범위
                sentenceBitMin = Math.random() * 3 + 1; // 1-4 범위
            }
            
            // 제목 요소에 모든 NB 관련 속성 저장 (HTML에 직접 반영)
            element.setAttribute('data-nb-max', sentenceBitMax);
            element.setAttribute('data-nb-min', sentenceBitMin);
            element.setAttribute('data-nb-text', element.textContent);
            element.setAttribute('data-sentence-bit-max', sentenceBitMax);
            element.setAttribute('data-sentence-bit-min', sentenceBitMin);
            
            // 툴팁으로 NB 값 표시
            element.setAttribute('title', `[NB] max: ${sentenceBitMax}, min: ${sentenceBitMin}`);
            
            // 상위 header-content 요소에도 NB 값 저장
            const headerContent = element.closest('.header-content');
            if (headerContent) {
                headerContent.setAttribute('data-nb-max', sentenceBitMax);
                headerContent.setAttribute('data-nb-min', sentenceBitMin);
                headerContent.setAttribute('data-nb-text', element.textContent);
                headerContent.setAttribute('data-sentence-bit-max', sentenceBitMax);
                headerContent.setAttribute('data-sentence-bit-min', sentenceBitMin);
                headerContent.setAttribute('title', `[NB] max: ${sentenceBitMax}, min: ${sentenceBitMin}`);
                console.log('header-content에 NB 값 저장:', sentenceBitMax, sentenceBitMin);
            }
            
            // NB 값은 HTML 속성에만 저장하고 화면에는 표시하지 않음
            
            // 단어 처리 (중복 제거)
            words.forEach(word => {
                if (!this.processedWords.has(word)) {
                    this.addWordToDisplay(word, sentenceBitMax, sentenceBitMin);
                    this.processedWords.add(word);
                    console.log('단어 처리 완료:', word);
                }
            });

            this.currentIndex++;

            // UI 업데이트
            this.elements.progressBar.style.width = `${this.progress}%`;

        } catch (error) {
            console.error('scanNext 오류:', error);
        }
    }

    addWordToDisplay(word, bitMax, bitMin) {
        if (!this.elements.wordList) {
            console.error('wordList 요소를 찾을 수 없습니다.');
            return;
        }

        try {
            // 단어별 고유 ID 생성 (중복 허용)
            const wordId = `${word}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
            
            const wordElement = document.createElement('div');
            wordElement.className = 'word-item';
            wordElement.dataset.bitMax = bitMax;
            wordElement.dataset.bitMin = bitMin;
            wordElement.dataset.wordId = wordId;
            wordElement.innerHTML = `${word}`;

            // 버튼별 상태를 클래스 레벨에서 관리
            if (!this.buttonStates) {
                this.buttonStates = new Map();
            }
            


            wordElement.onclick = () => {
                try {
                    // 기존 interval 중지 (다른 버튼 클릭 시)
                    if (this.searchInterval) {
                        clearInterval(this.searchInterval);
                        this.searchInterval = null;
                        console.log('기존 검색 중지');
                    }

                    // 이전 하이라이트 제거 (모든 애니메이션 종료)
                    this.clearAllAnimations();

                    // NB 조건 필터링을 위한 값 가져오기
                    const buttonBitMax = parseFloat(wordElement.dataset.bitMax);
                    const buttonBitMin = parseFloat(wordElement.dataset.bitMin);

                    // 조건 1: 텍스트 포함 조건으로 필터링
                    const textMatchedElements = this.allElements.filter(element => {
                        return element && element.textContent && 
                               element.textContent.includes(word);
                    });
                    
                    // 조건 2: NB 유사도 50% 이상 조건으로 필터링
                    const nbMatchedElements = this.allElements.filter(element => {
                        // header-content 요소에서 NB 값 확인 (우선) 또는 제목 요소에서 확인
                        let elementSentenceBitMax, elementSentenceBitMin;
                        
                        const headerContent = element.closest('.header-content');
                        if (headerContent && headerContent.dataset.nbMax) {
                            // header-content에 NB 값이 있으면 사용
                            elementSentenceBitMax = parseFloat(headerContent.dataset.nbMax);
                            elementSentenceBitMin = parseFloat(headerContent.dataset.nbMin);
                        } else {
                            // 없으면 제목 요소에서 확인
                            elementSentenceBitMax = parseFloat(element.dataset.nbMax);
                            elementSentenceBitMin = parseFloat(element.dataset.nbMin);
                        }
                        
                        // NB 조건: 유사도 50% 이상
                        const maxDiff = Math.abs(elementSentenceBitMax - buttonBitMax);
                        const minDiff = Math.abs(elementSentenceBitMin - buttonBitMin);
                        const maxSimilarity = Math.max(0, 100 - (maxDiff / buttonBitMax) * 100);
                        const minSimilarity = Math.max(0, 100 - (minDiff / buttonBitMin) * 100);
                        const avgSimilarity = (maxSimilarity + minSimilarity) / 2;
                        
                        return !isNaN(elementSentenceBitMax) && !isNaN(elementSentenceBitMin) &&
                               avgSimilarity >= 50; // 50% 이상 유사도
                    });
                    
                    // 랜덤으로 두 조건 중 하나 선택
                    const useTextCondition = Math.random() < 0.5; // 50% 확률
                    const matchedElements = useTextCondition ? textMatchedElements : nbMatchedElements;
                    const selectedCondition = useTextCondition ? '텍스트 포함' : 'NB 유사도 50% 이상';
                    
                    console.log('조건 선택:', {
                        word: word,
                        selectedCondition: selectedCondition,
                        textMatchedCount: textMatchedElements.length,
                        nbMatchedCount: nbMatchedElements.length,
                        finalMatchedCount: matchedElements.length
                    });

                    if (matchedElements.length > 0) {
                        // setInterval로 전체 페이지 순회 시작
                        let currentIndex = 0;
                        
                                        this.searchInterval = setInterval(() => {
                    // 이전 하이라이트 제거 (모든 애니메이션 종료)
                    this.clearAllAnimations();
                    
                    // 현재 요소 선택
                    const element = matchedElements[currentIndex];
                    
                    // 조건에 따라 다른 하이라이트 적용
                    if (selectedCondition === '텍스트 포함') {
                        // 텍스트 포함 조건: 단어에만 하이라이트
                        this.highlightWordInElement(element, word);
                    } else {
                        // NB 유사도 조건: 전체 문장에 하이라이트
                        this.highlightSentenceInElement(element);
                    }
                    
                    // 스크롤 실행
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // 유사도 계산
                    const headerContent = element.closest('.header-content');
                    let elementSentenceBitMax, elementSentenceBitMin;
                    
                    if (headerContent && headerContent.dataset.nbMax) {
                        elementSentenceBitMax = parseFloat(headerContent.dataset.nbMax);
                        elementSentenceBitMin = parseFloat(headerContent.dataset.nbMin);
                    } else {
                        elementSentenceBitMax = parseFloat(element.dataset.nbMax);
                        elementSentenceBitMin = parseFloat(element.dataset.nbMin);
                    }
                    
                    const maxDiff = Math.abs(elementSentenceBitMax - buttonBitMax);
                    const minDiff = Math.abs(elementSentenceBitMin - buttonBitMin);
                    const maxSimilarity = Math.max(0, 100 - (maxDiff / buttonBitMax) * 100);
                    const minSimilarity = Math.max(0, 100 - (minDiff / buttonBitMin) * 100);
                    const avgSimilarity = (maxSimilarity + minSimilarity) / 2;

                    console.log(`순회 검색 - ${selectedCondition} 조건 만족 요소 발견:`, {
                        word: word,
                        tag: element.tagName,
                        text: element.textContent.trim(),
                        position: `${currentIndex + 1}/${matchedElements.length}`,
                        condition: selectedCondition,
                        similarity: selectedCondition === 'NB 유사도 50% 이상' ? 
                            `유사도: ${avgSimilarity.toFixed(1)}% (MAX: ${maxSimilarity.toFixed(1)}%, MIN: ${minSimilarity.toFixed(1)}%)` : 
                            '텍스트 포함'
                    });

                    // 다음 요소로 이동
                    currentIndex++;
                    
                    // 조건 1: 50% 이상 유사도일 때 10% 확률로 랜덤 선택
                    if (avgSimilarity >= 50) {
                        if (Math.random() < 0.1) { // 10% 확률
                            clearInterval(this.searchInterval);
                            this.searchInterval = null;
                            console.log(`랜덤 선택 완료 - ${selectedCondition} 조건에서 10% 확률로 선택:`, {
                                word: word,
                                tag: element.tagName,
                                text: element.textContent.trim(),
                                position: `${currentIndex + 1}/${matchedElements.length}`,
                                condition: selectedCondition,
                                similarity: selectedCondition === 'NB 유사도 50% 이상' ? 
                                    `${avgSimilarity.toFixed(1)}%` : '텍스트 포함',
                                selection: '랜덤 선택 (10% 확률)'
                            });
                            return;
                        }
                    }
                    
                    // 조건 2: 순환이 완료되면 종료 (모든 요소를 한 번씩 순회했을 때)
                    if (currentIndex >= matchedElements.length) {
                        clearInterval(this.searchInterval);
                        this.searchInterval = null;
                        console.log(`순회 완료 - ${selectedCondition} 조건의 모든 요소를 한 번씩 순회했습니다. (랜덤 선택 없음)`);
                        return;
                    }
                }, 1); // 1ms마다 순회

                        console.log(`${selectedCondition} 검색 순회 시작:`, {
                            word: word,
                            totalElements: matchedElements.length,
                            interval: '1ms',
                            condition: selectedCondition
                        });
                    } else {
                        console.log(`${selectedCondition} 조건을 만족하는 요소가 없습니다:`, {
                            word: word,
                            buttonBitMax: buttonBitMax,
                            buttonBitMin: buttonBitMin,
                            condition: selectedCondition,
                            textMatchedCount: textMatchedElements.length,
                            nbMatchedCount: nbMatchedElements.length
                        });
                    }
                } catch (error) {
                    console.error('클릭 이벤트 처리 중 오류:', error);
                }
            };

            this.elements.wordList.appendChild(wordElement);
            this.elements.wordTotal.textContent = `총 ${this.processedWords.size}개 단어`;
            console.log('단어 표시 완료:', word);

        } catch (error) {
            console.error('단어 표시 중 오류:', error);
        }
    }

    start() {
        if (this.isRunning) {
            console.log('이미 스캔이 실행 중입니다.');
            return;
        }
        
        try {
            console.log('스캔 시작');
            this.isRunning = true;
            this.progress = 0;
            this.currentIndex = 0;
            
            // 상태 업데이트
            this.elements.status.textContent = "제목 스캔 시작...";
            this.elements.wordCount.textContent = "0%";
            this.elements.progressBar.style.width = "0%";
            
            // 스캔할 요소가 있는지 확인
            if (this.allElements.length === 0) {
                console.warn('스캔할 요소가 없습니다.');
                this.elements.status.textContent = "스캔할 요소가 없습니다.";
                this.isRunning = false;
                return;
            }
            
            // 스캔 시작
            this.intervalId = setInterval(() => {
                this.scanNext();
            }, this.scanSettings.scanInterval);

            console.log('스캔 시작됨');
        } catch (error) {
            console.error('스캔 시작 오류:', error);
        }
    }

    complete() {
        clearInterval(this.intervalId);
        this.isRunning = false;
        this.elements.status.textContent = "요소 스캔 완료";
        this.elements.wordCount.textContent = 
            `완료 (${this.allElements.length}개 요소)`;
        
        // 마지막 하이라이트 제거
        if (this.lastScannedElement) {
            this.lastScannedElement.style.backgroundColor = '';
        }
        
        console.log('스캔 완료');
    }
}

// 스크립트 로드 함수
function loadScript(url) {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${url}"]`)) {
            resolve();
            return;
        }
        
        const script = document.createElement('script');
        script.src = url;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// 필수 함수 체크
function checkDependencies() {
    const required = ['wordNbUnicodeFormat', 'BIT_MAX_NB', 'BIT_MIN_NB'];
    const missing = required.filter(func => typeof window[func] !== 'function');
    
    if (missing.length > 0) {
        console.warn('일부 함수가 없습니다:', missing.join(', '));
        console.log('기본값으로 스캐너가 작동합니다.');
        return false;
    }
    console.log('모든 필수 함수 확인 완료');
    return true;
}

// 초기화 함수
function initializeScanner() {
    try {
        console.log('스캐너 초기화 시작');
        
        // 스캐너 인스턴스 생성 (의존성 체크는 선택적)
        if (typeof checkDependencies === 'function') {
            if (!checkDependencies()) {
                console.warn('일부 의존성이 로드되지 않았지만 스캐너를 실행합니다.');
            }
        }
        
        // 스캐너 인스턴스 생성
        window.nbScanner = new NBScanner();
        
    } catch (error) {
        console.error('스캐너 초기화 실패:', error);
    }
}

// 페이지 로드 완료 후 초기화
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM 준비됨');
    
    // 페이지 로드 완료 대기
    window.addEventListener('load', function() {
        console.log('페이지 로드 완료');
        
        // bitCalculation.js 로드 확인 및 로드
        function loadBitCalculation() {
            return new Promise((resolve, reject) => {
                // 이미 로드되어 있는지 확인 (더 엄격한 체크)
                if (typeof window.wordNbUnicodeFormat === 'function' && 
                    typeof window.BIT_MAX_NB === 'function' && 
                    typeof window.BIT_MIN_NB === 'function' &&
                    typeof window.SUPER_BIT !== 'undefined') {
                    console.log('bitCalculation.js 이미 로드됨 (함수 확인됨)');
                    resolve();
                    return;
                }
                
                // 이미 스크립트 태그가 있는지 확인
                const existingScripts = document.querySelectorAll('script[src*="bitCalculation.js"]');
                if (existingScripts.length > 0) {
                    console.log('bitCalculation.js 스크립트 태그가 이미 존재합니다. 기존 것 사용.');
                    // 기존 스크립트가 로드될 때까지 대기
                    setTimeout(() => {
                        if (typeof window.wordNbUnicodeFormat === 'function') {
                            resolve();
                        } else {
                            reject(new Error('기존 스크립트 로드 실패'));
                        }
                    }, 2000);
                    return;
                }
                
                console.log('bitCalculation.js 로드 중...');
                
                // 여러 경로 시도
                const possiblePaths = [
                    '../../_8비트/js/bitCalculation.js',
                    '../_8비트/js/bitCalculation.js',
                    '_8비트/js/bitCalculation.js',
                    '/_8비트/js/bitCalculation.js'
                ];
                
                let loaded = false;
                let errorCount = 0;
                
                possiblePaths.forEach((path, index) => {
                    if (loaded) return;
                    
                    const script = document.createElement('script');
                    script.src = path;
                    script.onload = () => {
                        console.log(`bitCalculation.js 로드 성공: ${path}`);
                        loaded = true;
                        resolve();
                    };
                    script.onerror = () => {
                        errorCount++;
                        console.warn(`bitCalculation.js 로드 실패: ${path}`);
                        
                        if (errorCount === possiblePaths.length) {
                            console.error('모든 경로에서 bitCalculation.js 로드 실패');
                            reject(new Error('bitCalculation.js를 찾을 수 없습니다'));
                        }
                    };
                    document.head.appendChild(script);
                });
            });
        }
        
        // 스캐너 초기화 (bitCalculation.js 없이도 실행)
        console.log('스캐너 초기화 시작');
        setTimeout(() => {
            try {
                window.nbScanner = new NBScanner();
                console.log('스캐너 초기화 완료');
            } catch (error) {
                console.error('스캐너 초기화 실패:', error);
            }
        }, 1000);
    });
});

</script>