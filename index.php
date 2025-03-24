<?php include "partials/head.php"; ?>
<body>
    
    <?php include "partials/loading-screen.php"; ?>
    
    <?php include "partials/menu.php"; ?>

    <?php include "partials/header.php"; ?>
    
    <main id="main-content" class="opacity-0">
        
        <!-- Hero Section-->
        <section class="mast opacity-0" style="background-color: #f6f5f1;">
            <div class="container max-w-7xl mx-auto lg:pt-20 lg:pb-10">
                <div class="mast__header">
                    <h1 class="mast__title js-spanize canela-text font-medium font-size-superXL">
                        Hayatı Anlamlandırmanın Yolu: <strong>Yavaşlamak</strong>
                    </h1> 
                    <hr class="sep"/>
                    <p class="mast__text js-spanize">Hayatın hızında sürükleniyoruz. Durmadan ilerliyor, bir hedefin ardından diğerine koşuyoruz. Ancak sonunda gerçekten neye ulaşıyoruz? <strong>Elimizdeki başarılar</strong>, içimizdeki boşluğu dolduruyor mu? Yoksa tüm bu telaş, bizi kendimizden ve anlamdan biraz daha uzaklaştıran bir illüzyon mu? Bu sorular, sadece yaşamı sorgulayanlar için değil, bir an durup gerçekten düşünen herkes için önemlidir.</p>

                    <a href="#miray-polat" class="hover-button mt-5" id="mast-button">
                        <div class="first">Klinik Psikolog Miray Polat</div>
                        <div class="slidein">Yavaşlama Rehberin</div>
                    </a>
                </div>
                <div class="mast__bg" style="background-image:url('uploads/load-01.png')"></div>
            </div>
        </section> 

        <!-- Miray Polat Kimdir -->
        <section class="bg-orange-50 relative" id="miray-polat">
            <div class="container max-w-7xl mx-auto px-4 lg:py-16">
                <div class="flex flex-col md:flex-row gap-8 md:gap-12">
                    <!-- Sol taraf - Resim -->
                    <div class="w-full lg:w-1/3 pt-6 lg:py-12">
                        <div class="relative aspect-[3/4] w-full rounded-lg overflow-hidden shadow-xl">
                            <img 
                                src="uploads/psikolog-miray-polat-hakkinda.jpeg" 
                                alt="Klinik Psikolog Miray Polat" 
                                class="w-full h-full object-cover object-center"
                            />
                        </div>
                    </div>

                    <!-- Sağ taraf - İçerik -->
                    <div class="w-full md:w-2/3">
                        <h2 class="canela-text text-3xl md:text-4xl font-bold mb-4 lg:mb-8 text-slate-800">
                            Klinik Psikolog Miray Polat
                        </h2>
                        
                        <p class="fade mb-6 text-lg text-gray-600 leading-relaxed">
                            Psikoloji lisans eğitimimi Üsküdar Üniversitesi'nde tamamladıktan sonra, aynı üniversitede Klinik Psikoloji yüksek lisansımı yaptım. Akademik sürecimin ardından, psikoterapi alanında farklı ekollerde eğitimler alarak mesleki yetkinliğimi geliştirdim. <br class="hidden md:block" /> Şu anda varoluşçu psikoterapi ekolüyle çalışmakta ve bireylerin yaşam deneyimlerini anlamlandırmalarına destek olmaktayım.
                        </p>
                        
                        <p class="fade mb-6 text-gray-600 leading-relaxed">
                            Varoluşçu psikoterapi, bireyin kendisiyle ve dünyayla kurduğu ilişkinin derinliklerine inmeyi amaçlayan, özgürlük, sorumluluk, anlam arayışı ve otantik yaşam gibi kavramlara odaklanan bir yaklaşımdır. Bu terapi anlayışı doğrultusunda, danışanlarıma yaşamsal sorularını keşfetmeleri, kendi değerlerini belirlemeleri ve varoluşlarını daha bilinçli bir şekilde deneyimlemeleri için alan açıyorum. Terapötik süreçte, bireyin kendi varoluşsal sınırlarını keşfetmesine, içsel kaynaklarını fark etmesine ve özgün bir yaşam anlayışı geliştirmesine rehberlik ediyorum.
                        </p>
                        
                        <p class="fade mb-8 text-gray-600">
                            Zihinsel ve bedensel farkındalık üzerine çalışmalarım, mindfulness ve yoga ile şekillendi. Bu yaklaşımlar, terapi sürecinde bütüncül bir bakış açısı kazanmama katkı sağladı. Psikoloji, sanat ve beden farkındalığını bir araya getiren <strong>Yavaşlama Rehberi</strong> projesini bu doğrultuda hayata geçirdim. Burada, modern hayatın hızına karşı bilinçli bir yavaşlama sürecini destekleyen çalışmalar yürütüyor, danışanlarımın kendi ritimlerini bulmalarına yardımcı oluyorum.
                        </p>

                        <p class="fade mb-8 text-gray-600 lg:max-w-3xl">
                            Bireysel terapi hizmetlerim ve atölyelerim hakkında daha fazla bilgi için iletişime geçelim.
                        </p>
                        
                        <a href="iletisim" class="fade bg-slate-blue text-white px-4 lg:px-8 py-2 lg:py-3 rounded-md lg:text-lg inline-block hover:bg-deep-blue transition-colors duration-300 mb-8 lg:mb-0" id="gercekler">
                            İletişime geçelim
                            <span class="text-2xl">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Yavaş Adımlar Intro Section -->
        <section class="bg-cream relative py-10 md:py-16" id="yavas-adimlar-intro">
            <div class="container max-w-7xl mx-auto px-4">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-3 fade canela-text">
                    Daha Yavaş Adımlarla:
                </h2>
                
                <!-- Adımlar Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-8 lg:mt-16">
                    <!-- Adım 1 -->
                    <div class="relative group fade">
                        <div class="bg-sky-light rounded-2xl p-4 lg:p-8 flex flex-col items-center justify-center transform transition-transform duration-300 group-hover:-translate-y-2">
                            <div class="w-40 h-40">
                                <img src="uploads/pattern-001.png" alt="Kendi Gerçeğini Bul" class="w-full h-full object-cover object-center">
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-center text-gray-800">Kendi Gerçeğini Bul</h3>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-12 h-3 bg-blue-200 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>

                    <!-- Adım 2 -->
                    <div class="relative group fade">
                        <div class="bg-red-50 rounded-2xl p-4 lg:p-8 flex flex-col items-center justify-center transform transition-transform duration-300 group-hover:-translate-y-2">
                            <div class="w-40 h-40">
                                <img src="uploads/pattern-002.png" alt="Kendi Gerçeğini Bul" class="w-full h-full object-cover object-center">
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-center text-gray-800">Seçimlerin Gücü</h3>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-12 h-3 bg-red-200 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>

                    <!-- Adım 3 -->
                    <div class="relative group fade">
                        <div class="bg-green-50 rounded-2xl p-4 lg:p-8 flex flex-col items-center justify-center transform transition-transform duration-300 group-hover:-translate-y-2">
                            <div class="w-40 h-40">
                                <img src="uploads/pattern-003.png" alt="Kendi Gerçeğini Bul" class="w-full h-full object-cover object-center">
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-center text-gray-800">Bağlantılar ve Katkı</h3>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-12 h-3 bg-green-200 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>

                    <!-- Adım 4 - -->
                    <div class="relative group fade">
                        <div class="bg-yellow-50 rounded-2xl p-4 lg:p-8 flex flex-col items-center justify-center transform transition-transform duration-300 group-hover:-translate-y-2">
                            <div class="w-40 h-40">
                                <img src="uploads/pattern-004.png" alt="Kendi Gerçeğini Bul" class="w-full h-full object-cover object-center">
                            </div>
                            <h3 class="text-xl font-semibold mb-3 text-center text-gray-800">Anlamını Yarat</h3>
                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-12 h-3 bg-yellow-200 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>
                </div>

                <!-- Açıklama Metni -->
                <div class="max-w-3xl mx-auto mt-6 lg:mt-16 text-center fade">
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Yavaşlama yolculuğunda sizi dört temel adım bekliyor. Her adım, kendinizi ve yaşamınızı daha derinden anlamanız için tasarlanmış bir keşif süreci. Bu adımları kendi hızınızda, kendi ritminizde deneyimleyebilirsiniz.
                    </p>
                </div>

                <!-- Scroll İndikatör -->
                <div class="absolute bottom-1 lg:bottom-4 left-1/2 transform -translate-x-1/2 fade">
                    <div class="animate-bounce">
                        <svg class="w-6 h-6 text-deep-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Birinci Adım -->
        <section class="relative bg-sky-light overflow-hidden" id="birinci-adim">
            <!-- Arka plan numarası -->
            <div class="absolute -right-[70px] -bottom-[40px] lg:-right-32 transform select-none pointer-events-none opacity-10">
                <span class="text-[600px] md:text-[800px] font-bold text-gray-900 canela-text leading-none">
                    1
                </span>
            </div>

            <div class="container max-w-4xl mx-auto px-8 py-16 md:py-24 relative z-10">
                <img src="uploads/pattern-001.png" alt="Kendi Gerçeğini Bul" class="h-24 lg:h-40 object-center mb-3">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">
                    Kendi Gerçeğini Bul
                </h2>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed">İnsan, çoğu zaman <strong>kendine en uzak</strong> varlıktır. İçsel <u>hakikatimiz</u>, yaşamın kaosunda, çevrenin dayattığı <u>rollerde veya kendi korkularımızda</u> kaybolur. Kim olduğumuz sorusu, belki de <u>en temel</u> ama <u>en zor sorular</u>dan biridir. <strong>Varoluşçu perspektiften bakıldığında</strong>, insanın gerçeği, <u>hazır bir cevaptan ziyade</u> <strong>sürekli inşa</strong> edilen bir süreçtir. <strong>Özgürlüğümüz</strong>, bu inşa sürecinin en büyük <u>yükü</u> ve <strong>en değerli</strong> armağanıdır.</p>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed"><strong>Gerçeğini bulmak</strong>, <u>hazırda bekleyen</u> bir <strong><u>özü keşfetmek değil</u></strong>, tam da yaşamın ortasında <strong>kendini yaratmaktır</strong>. Sartre'ın dediği gibi, "<em>İnsan önce var olur, sonra kendini tanımlar.</em>" Ancak bu tanım, <u>başkalarının çizdiği sınırlar</u> ya da toplumsal <strong>kalıplarla yapılırsa</strong>, insan, <u>kendini özgün kılma şansı</u>nı kaybeder. <em>Gerçeğini bulmak</em>, bu <strong><u>kalıpları sorgulamakla başlar</u></strong>. Gerçekten <u>bize ait olmayan</u>, sadece <strong>içselleştirdiğimiz bir fikir mi taşıyoruz</strong>? Yoksa <u>bu fikirler</u> <strong>bizi biz yapan</strong> şeyler mi?</p>
                
                <a href="gercekler" class="fade bg-slate-blue text-white px-4 lg:px-8 py-2 lg:py-3 rounded-md lg:text-lg inline-block mx-auto hover:bg-deep-blue transition-colors duration-300" id="gercekler">
                    Gerçeğini bulmanın anlamı
                    <span class="text-2xl">→</span>
                </a>
            </div>
        </section>

        <!-- İkinci Adım -->
        <section class="bg-red-50 relative overflow-hidden" id="ikinci-adim">
            <!-- Arka plan numarası -->
            <div class="absolute -left-[70px] -bottom-[20px] lg:-bottom-[70px] lg:-left-32 transform select-none pointer-events-none opacity-10">
                <span class="text-[600px] md:text-[800px] font-bold text-gray-900 canela-text leading-none">
                    2
                </span>
            </div>

            <div class="container max-w-4xl mx-auto px-8 py-16 md:py-24 relative z-10">
                <img src="uploads/pattern-002.png" alt="Seçimlerin Gücü" class="h-24 lg:h-40 object-center mb-3">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">
                    Seçimlerin Gücü    
                </h2>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed"><strong>İnsan</strong>, varoluşsal anlamda <strong>özgürdür</strong>, fakat bu özgürlük, <u>sürekli bir seçim yapmak</u> yükümlülüğüyle gelir. <strong>Seçim</strong>, <u>hayatı anlamlandırmanın</u> temel taşıdır; çünkü her seçim, bizi hem varoluşsal olarak <strong>şekillendirir</strong> hem de gerçekliğimize <strong>yakınlaştırır</strong>. <u>Ancak bu</u> özgürlük, aynı zamanda bir <strong>sorumluluk taşır</strong>. Her an, <strong>yaşamın anlamını yeniden yaratmak</strong> gücüne sahipken, çoğu zaman bu <u>gücü fark etmeden</u>, <strong>alışkanlıklarımıza</strong> ve <strong>dışsal baskılara</strong> <strong><u>teslim oluruz</u></strong>. Oysa her seçim, <u>bir yeniden doğuşu</u>, <u>bir varlık halini</u> <strong>doğurur</strong>.
                </p>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed">Birçok felsefi okulda, özellikle varoluşçulukta, <strong>seçimler</strong> <u>insanın özünü bulduğu</u> ve kendini <strong>ifade ettiği</strong> <u>en özgür</u> alanlardır. Jean-Paul Sartre'a göre; insan, <u>özünü</u>, <strong>seçimleriyle yaratır</strong>; <u>biz, varoluşumuzun sorumluluğunu taşırken</u>, aynı zamanda bu <strong>seçimlerle</strong> <u>kendi anlamımızı inşa</u> ederiz.
                </p>
                
                
                <a href="gercekler" class="fade bg-slate-blue text-white px-4 lg:px-8 py-2 lg:py-3 rounded-md lg:text-lg inline-block mx-auto hover:bg-deep-blue transition-colors duration-300" id="gercekler">
                    Bu adımı seçmenin sebepleri
                    <span class="text-2xl">→</span>
                </a>
                
            </div>
        </section>

        <!-- Üçüncü Adım -->
        <section class="bg-green-50 relative overflow-hidden" id="ucuncu-adim">
            <!-- Arka plan numarası -->
            <div class="absolute -right-[40px] bottom-0 lg:-right-32 transform select-none pointer-events-none opacity-10">
                <span class="text-[600px] md:text-[800px] font-bold text-gray-900 canela-text leading-none">
                    3
                </span>
            </div>

            <div class="container max-w-4xl mx-auto px-8 py-16 md:py-20 relative z-10">
                <img src="uploads/pattern-003.png" alt="Bağlantılar ve Katkı" class="h-24 lg:h-40 object-center mb-3">

                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">
                    Bağlantılar ve Katkı
                </h2>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed">Hayatta <strong><u>kim olduğumuz</u></strong> kadar, <strong>kimlerle</strong> ve <strong>neyle</strong> <u>bağ kurduğumuz</u> da önemlidir. İnsan, ancak bir <u>bağın içinde anlam bulabilir</u>. Ancak <strong>modern dünyada</strong> bu bağlar, <strong><u>hızın</u> ve yüzeyselliğin</strong> gölgesinde çoğu zaman <strong>zayıflar</strong>. Karşılaştığımız <u>yüzler birer siluet</u>, <u>konuşmalar birer yankı</u>, <u>ilişkiler birer alışveriş</u> haline gelir. <strong>Oysa</strong> insan, <strong>gerçek anlamı</strong> bu <strong><u>yüzeyselliğin</u> ötesinde</strong>, <u>bağ kurarak</u> ve <strong>katkıda bulunarak</strong> yaratır. Bu <u>bağlar ve katkılar</u>, yalnızca <u>başkalarına sunduklarımız</u> <strong>değil</strong>, aynı zamanda <strong>kendi varlığımızı yeniden</strong> inşa ettiğimiz <u>köprülerdir</u>.</p>
                
                <p class="fade mb-6 text-lg text-gray-600 leading-relaxed"><u>Varoluşsal</u> bir <u>perspektif</u>ten bakıldığında, <strong></strong>bağlanma ve katkı</strong>, sadece <u>diğerleriyle değil</u>, <u><strong>hayatın kendisiyle kurduğumuz</strong> ilişkinin bir ifadesidir</u>. <strong>Heidegger</strong>, "<em>varlık, ancak diğer varlıklarla birlikte anlama kavuşur</em>" derken, insanın <strong>sosyal</strong> ve <strong>ilişkisel doğasına</strong> dikkat çeker. Bu bağlar, yalnızca <strong><u>dış dünyamızda</u></strong> değil, <strong><u>içsel dünyamızda</u></strong> da derin bir yankı bulur.
                </p>
                
                
                <a href="gercekler" class="lg:mt-2 fade bg-slate-blue text-white px-4 lg:px-8 py-2 lg:py-3 rounded-md lg:text-lg inline-block mx-auto hover:bg-deep-blue transition-colors duration-300" id="gercekler">
                    Bağlantının katkısı
                    <span class="text-2xl">→</span>
                </a>
                
            </div>
        </section>

        <!-- Dördüncü Adım -->
        <section class="bg-yellow-50 relative overflow-hidden" id="dorduncu-adim">
            <!-- Arka plan numarası -->
            <div class="absolute -left-[40px] bottom-0 lg:bottom-[20px] lg:-left-32 transform select-none pointer-events-none opacity-10">
                <span class="text-[600px] md:text-[800px] font-bold text-gray-900 canela-text leading-none">
                    4
                </span>
            </div>

            <div class="container max-w-4xl mx-auto px-8 py-12 md:py-24 relative z-10">
                <img src="uploads/pattern-004.png" alt="Bağlantılar ve Katkı" class="h-24 lg:h-40 object-center mb-3">

                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">
                    Anlamı Yaratmak
                </h2>
                
                <p class="fade mb-6 text-lg text-gray-600">İnsan, kendi varoluşuyla yüzleştiğinde kaçınılmaz bir soruyla karşılaşır: "Bütün bunların anlamı ne?" Bu soru bazen bir boşluğun içinden, bazen bir dönüşüm sürecinin eşiğinden yükselir. <strong>Ancak varoluşçu bakış açısı bize şunu hatırlatır: Anlam, keşfedilmesi gereken bir gerçeklik değil, yaratılması gereken bir süreçtir.</strong>
                </p>
                
                <p class="fade mb-6 text-lg text-gray-600">Anlam arayışı, insanın kendisiyle ve dünya ile kurduğu ilişkinin en derin dinamiklerinden biridir. Özgürlüğümüz, bize bu anlamı yaratma sorumluluğunu da yükler. Anlam dışarıdan gelen bir şey değildir; kendi varoluşuna nasıl dokunduğun, dünyada nasıl bir iz bıraktığınla şekillenir. Bu yüzden "Anlamı Yaratmak" adımı, sadece bir kavrayış değil, bilinçli bir varoluş pratiğidir.</p>
                
                
                <a href="gercekler" class="fade bg-slate-blue text-white px-4 lg:px-8 py-2 lg:py-3 rounded-md lg:text-lg inline-block mx-auto hover:bg-deep-blue transition-colors duration-300" id="gercekler">
                    Anlamı yaratmanın ihtiyacı
                    <span class="text-2xl">→</span>
                </a>
                
            </div>
        </section>

        <!-- Yavaşlama Kulübü -->
        <section class="bg-white relative py-16 md:py-16" id="library">
            <div class="container max-w-7xl mx-auto px-4">
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-6 fade canela-text">
                    Yavaşlama Kulübü
                </h2>
                <p class="text-gray-600 text-center mb-12 fade max-w-2xl mx-auto">Okuduğum ve anlamladırdığım özetlerle, varoluşçu psikoterapi, felsefe ve kişisel gelişim alanlarında seçilmiş en iyi eserler.</p>
                
                <!-- Kitap Grid -->
                <div class="mb-14 relative">
                    <div class="flex flex-col space-y-8 md:space-y-0 md:flex-row justify-center relative w-full">
                        <!-- Kitap Rafı -->
                        <div class="hidden md:block book-shelf absolute bottom-0 w-full">
                            <div class="shelf"></div>
                            <div class="shelf-shadow"></div>
                        </div>
                        
                        <!-- Kitaplar -->
                        <div class="perspective-container">
                            <div class="book-wrap">
                                <div  class="book w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer" style="background: url('uploads/kitaplar/erich-fromm-olma-sanati.jpg') no-repeat center center; background-size: cover;"></div>
                                <div class="title"></div>
                                <div class="book-back w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer">
                                    <div class="text">
                                        <h3 class="text-white text-xl font-bold mb-3">Olma Sanatı</h3>
                                        
                                        <p class="mb-4 text-orange-100">Eric Fromm'un Olma Sanatı kitabı, insanı derin bir yolculuğa davet eden bir eser. Modern dünyada, mutluluğu sahip olduklarımızda arama alışkanlığımızı sorgulayan Fromm, bunun yerine "olma" haline yönelmemizi öneriyor.</p>

                                        <a class="hover:underline hover:text-white text-white transition-colors duration-100 font-bold" href="kitap-detayları">
                                            Detaylar →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="perspective-container">
                            <div class="book-wrap">
                                <div  class="book w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer" style="background: url('uploads/kitaplar/ben-ve-biz_postmodern-insanin-psikanalizi-400x622.jpg') no-repeat center center; background-size: cover;"></div>
                                <div class="title"></div>
                                <div class="book-back w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer">
                                    <div class="text">
                                        <h3 class="text-white text-xl font-bold mb-3">Ben ve Biz</h3>
                                        
                                        <p class="mb-4 text-orange-100">Eric Fromm'un Olma Sanatı kitabı, insanı derin bir yolculuğa davet eden bir eser. Modern dünyada, mutluluğu sahip olduklarımızda arama alışkanlığımızı sorgulayan Fromm, bunun yerine "olma" haline yönelmemizi öneriyor.</p>

                                        <a class="hover:underline hover:text-white text-white transition-colors duration-100 font-bold" href="kitap-detayları">
                                            Detaylar →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="perspective-container">
                            <div class="book-wrap">
                                <div class="book w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer" style="background: url('uploads/kitaplar/erich-fromm-onemi.jpg') no-repeat center center; background-size: cover;"></div>
                                <div class="title"></div>
                                <div class="book-back w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer">
                                    <div class="text">
                                        <h3 class="text-white text-xl font-bold mb-3">Erich Fromm'un Önemi</h3>
                                        
                                        <p class="mb-4 text-orange-100">Eric Fromm'un Olma Sanatı kitabı, insanı derin bir yolculuğa davet eden bir eser. Modern dünyada, mutluluğu sahip olduklarımızda arama alışkanlığımızı sorgulayan Fromm, bunun yerine "olma" haline yönelmemizi öneriyor.</p>

                                        <a class="hover:underline hover:text-white text-white transition-colors duration-100 font-bold" href="kitap-detayları">
                                            Detaylar →
                                        </a>
                                    </div>
                                </div>
                        </div>
                    </div>

                        <div class="perspective-container">
                            <div class="book-wrap">
                                <div  class="book w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer" style="background: url('uploads/kitaplar/0003094_sevme-sanati-modern-kapak-430x628.jpeg') no-repeat center center; background-size: cover;"></div>
                                <div class="title"></div>
                                <div class="book-back w-[240px] h-[400px] absolute top-0 left-0 bottom-0 right-0 mx-auto cursor-pointer">
                                    <div class="text">
                                        <h3 class="text-white text-xl font-bold mb-3">Sevme Sanatı</h3>
                                        
                                        <p class="mb-4 text-orange-100">Eric Fromm'un Olma Sanatı kitabı, insanı derin bir yolculuğa davet eden bir eser. Modern dünyada, mutluluğu sahip olduklarımızda arama alışkanlığımızı sorgulayan Fromm, bunun yerine "olma" haline yönelmemizi öneriyor.</p>

                                        <a class="hover:underline hover:text-white text-white transition-colors duration-100 font-bold" href="kitap-detayları">
                                            Detaylar →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- "Tüm Kitaplar" butonu -->
                <div class="text-center mt-16 mb-12 fade">
                    <a href="/library" class="inline-flex items-center gap-2 bg-deep-blue text-white px-8 py-3 rounded-lg hover:bg-slate-blue transition-colors duration-300 shadow-lg hover:shadow-xl text-lg">
                        <span>
                            Kulüp Kitaplığı
                            <span class="text-2xl">→</span>
                        </span>
                    </a>
                </div>
            </div>

            <!-- E-bülten Section -->
            <div class="bg-slate-blue/5 py-8 lg:py-16 mt-12">
                <div class="container max-w-7xl mx-auto px-4">
                    <div class="max-w-3xl mx-auto text-center">
                        <h3 class="text-2xl md:text-3xl font-bold mb-4 canela-text">
                            Yavaşlama Kulübüne Katılın
                        </h3>
                        <p class="text-gray-600 mb-8">Her an, yavaşlama yolculuğunuzda size eşlik edecek içgörüler, pratik öneriler ve ilham verici hikayeler için e-bültene katılın.</p>
                        
                        <form class="flex flex-col md:flex-row gap-4 max-w-xl mx-auto">
                            <div class="flex-1">
                                <input 
                                    type="email" 
                                    placeholder="E-posta adresiniz" 
                                    class="w-full px-6 py-4 rounded-lg border border-gray-200 focus:ring-2 focus:ring-deep-blue focus:border-transparent transition duration-200 bg-white/80"
                                    required
                    >
                </div>
                            <button 
                                type="submit" 
                                class="bg-deep-blue text-white px-8 py-4 rounded-lg hover:bg-slate-blue transition-colors duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 whitespace-nowrap"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                                Abone Ol
                            </button>
                        </form>
                        
                        <p class="text-sm text-gray-500 mt-4">
                            Gizliliğinize önem veriyoruz. E-postanızı asla paylaşmayacağız.
                            <a href="/privacy" class="text-deep-blue hover:underline">Gizlilik Politikası</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="container max-w-7xl mx-auto px-4 pt-10">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl md:text-4xl font-bold text-center mb-8 fade canela-text">
                        Güncel Blog Yazıları
                    </h2>
                    <p class="text-gray-600 mb-8">
                        Güncel makalaler, araştırma konuları, neden-sonuç ilişkileri üzerine birlikte derinleştiğimiz makaleler ve yazılar.
                    </p>
                </div>
                
                <!-- Blog Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Blog Card 1 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/growing@2x.BVm-B_ci_Z1TQ7M1.webp" 
                                alt="Blog 1" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Yavaşlamanın Psikolojik Faydaları</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">Modern yaşamın hızlı temposunda, yavaşlamanın zihinsel sağlığımız üzerindeki olumlu etkileri ve bu sürecin nasıl yönetilebileceği hakkında bilgiler...</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>

                    <!-- Blog Card 2 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/growing@2x.BVm-B_ci_Z1TQ7M1.webp" 
                                alt="Blog 2" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Mindfulness ve Günlük Yaşam</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">Günlük rutinlerimizde mindfulness pratiğini nasıl uygulayabiliriz? Basit ama etkili mindfulness teknikleri ve yaşam kalitemize etkileri...</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>

                    <!-- Blog Card 3 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/growing@2x.BVm-B_ci_Z1TQ7M1.webp" 
                                alt="Blog 3" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Dijital Detoks Rehberi</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">Teknoloji bağımlılığından kurtulmanın yolları, dijital detoksun mental sağlığımıza etkileri ve başarılı bir dijital detoks planı nasıl yapılır?</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>

                    <!-- Blog Card 4 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/ai-forest@2x.Dgu2JZ9k_Z1gYOuj.webp" 
                                alt="Blog 4" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Bilinçli Yaşam Pratikleri</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">Günlük hayatımızda bilinçli seçimler yapmanın önemi, farkındalık egzersizleri ve yaşam kalitemizi artıracak pratik öneriler...</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>

                    <!-- Blog Card 5 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/ai-forest@2x.Dgu2JZ9k_Z1gYOuj.webp" 
                                alt="Blog 5" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Stres Yönetimi Teknikleri</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">Modern hayatın getirdiği stresle başa çıkma yöntemleri, etkili nefes egzersizleri ve günlük stres azaltma rutinleri...</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>

                    <!-- Blog Card 6 -->
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden fade">
                        <div class="relative aspect-[16/9]">
                            <img 
                                src="https://maggieappleton.com/_astro/ai-forest@2x.Dgu2JZ9k_Z1gYOuj.webp" 
                                alt="Blog 6" 
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 text-gray-800">Sağlıklı İlişkiler Geliştirmek</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">İlişkilerimizde farkındalığın önemi, etkili iletişim teknikleri ve sağlıklı sınırlar oluşturmanın yolları...</p>
                            <a href="#" class="text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">Devamını Oku →</a>
                        </div>
                    </article>
                </div>

                <!-- "Tüm Yazılar" butonu -->
                <div class="text-center mt-12 fade">
                    <a href="/blog" class="inline-block bg-deep-blue text-white px-8 py-4 rounded-lg hover:bg-slate-blue transition-colors duration-300 shadow-lg hover:shadow-xl">
                        Yavaşlama Arşivi
                        <span class="text-2xl">→</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- instagram görselleri -->
        <section class="bg-orange-50">
            <div class="container max-w-7xl mx-auto px-4 py-10">
                <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                    <h2 class="fade text-2xl md:text-4xl font-bold text-center fade canela-text">
                        Instagram'da Yavaşlama Kulübü
                    </h2>
                    <a href="https://instagram.com/yavaslamarehberi" title="Yavaşlama Kulübü'nü Instagram'da Takip Et" target="_blank" class="fade text-deep-blue hover:text-slate-blue transition-colors duration-300 font-semibold">
                        Takip Et
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Instagram Görsel 1 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden fade aspect-3/4">
                        <a href="https://instagram.com/yavaslamarehberi" title="Yavaşlama Kulübü'nü Instagram'da Takip Et" target="_blank">
                            <img src="uploads/instagram/001.jpg" alt="Instagram 1" class="w-full h-full object-cover">
                        </a>
                    </div>
                    <!-- Instagram Görsel 2 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden fade aspect-3/4">
                        <a href="https://instagram.com/yavaslamarehberi" title="Yavaşlama Kulübü'nü Instagram'da Takip Et" target="_blank">
                            <img src="uploads/instagram/002.jpg" alt="Instagram 2" class="w-full h-full object-cover">
                        </a>
                    </div>
                    <!-- Instagram Görsel 3 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden fade aspect-3/4">
                        <a href="https://instagram.com/yavaslamarehberi" title="Yavaşlama Kulübü'nü Instagram'da Takip Et" target="_blank">
                            <img src="uploads/instagram/006.jpg" alt="Instagram 3" class="w-full h-full object-cover">
                        </a>
                    </div>
                    <!-- Instagram Görsel 4 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden fade aspect-3/4">
                        <a href="https://instagram.com/yavaslamarehberi" title="Yavaşlama Kulübü'nü Instagram'da Takip Et" target="_blank">
                            <img src="uploads/instagram/004.jpg" alt="Instagram 4" class="w-full h-full object-cover">
                        </a>
                    </div>
                    
                </div>
            </div>
        </section>

        <!-- İletişim Section -->
        <section class="bg-cream relative py-10 md:py-16" id="contact">
            <div class="container max-w-7xl mx-auto px-4">
                <img src="uploads/logo-yavaslama-rehberi.png" alt="İletişim" class="w-40 h-40 mb-8 mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold text-center mb-4 fade canela-text">İletişime Geçelim</h2>
                <p class="text-gray-600 text-center mb-12 fade max-w-3xl mx-auto"><strong>Sorularınız</strong>, düşünceleriniz ve <u>işbirliği önerileriniz</u> için <strong><u>iletişime geçelim</u></strong>.</p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                    <!-- Sol Taraf - İletişim Formu -->
                    <div class="bg-white p-8 rounded-lg shadow-2xl fade">
                        <h3 class="text-xl font-semibold mb-2 text-gray-800">Online Randevu</h3>
                        <p class="text-gray-600 mb-6">Randevu taleperinize mümkün olan en kısa sürede dönüş yapabilmem için bilgilerinizi bırakın.</p>
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Adınız Soyadınız
                                    </label>
                                    <input type="text" id="name" name="name" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-deep-blue focus:border-transparent transition duration-200" required>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numaranız</label>
                                    <input type="tel" id="tel" name="tel" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-deep-blue focus:border-transparent transition duration-200" required>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                        E-posta Adresiniz
                                    </label>
                                    <input type="text" id="subject" name="subject" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-deep-blue focus:border-transparent transition duration-200" required>
                                </div>
                                <div>
                                    <label for="newsletter" class="flex items-center gap-2">
                                        <input type="checkbox" id="newsletter" name="newsletter" class="form-checkbox h-5 w-5 text-deep-blue">
                                        <span class="text-sm text-gray-700">
                                            Yavaşlama Kulübü bültenimize katılmak ister misiniz?
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                    İletmek istedikleriniz...
                                </label>
                                <textarea id="message" name="message" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-deep-blue focus:border-transparent transition duration-200" required></textarea>
                            </div>

                            <button type="submit" class="w-full bg-deep-blue text-white px-8 py-4 rounded-lg hover:bg-slate-blue transition-colors duration-300 shadow-lg hover:shadow-xl">
                                Gönder
                            </button>
                        </form>
                    </div>

                    <!-- Sağ Taraf - İletişim Bilgileri -->
                    <div class="space-y-8 fade">
                        <!-- Adres -->
                        <div class="bg-white p-8 rounded-lg shadow-2xl">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">Suadiye Ofisim</h3>
                            <address class="text-gray-600 mb-4 not-italic">
                                Bağdat Caddesi, Köşk Apt. No: 428 <br>Kat: 4 - Daire: 7 - Suadiye <br>34710 İstanbul
                            </address>
                            <a href="https://goo.gl/maps/nJo3dTNSnaz3EGow7" title="Suadiye Ofisime Adres Tarifi Alın" target="_blank" class="text-gray-600 hover:text-deep-blue transition-colors duration-300 font-semibold underline underline-offset-4">
                                Adres tarifi alın
                            </a>
                        </div>

                        <!-- İletişim Bilgileri -->
                        <div class="bg-white p-8 rounded-lg shadow-2xl">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">İletişim Bilgilerim</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <svg class="w-6 h-6 text-deep-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <a href="mailto:bilgi@miraypolat.com" class="text-gray-600 hover:text-deep-blue transition-colors duration-300 underline underline-offset-4">
                                        bilgi@miraypolat.com
                                    </a>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <svg class="w-6 h-6 text-deep-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <a href="tel:+90 538 445 64 74" class="text-gray-600 hover:text-deep-blue transition-colors duration-300 underline underline-offset-4">
                                        +90 538 445 64 74
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Progress Bar -->
        <div id="progress-container" class="flex justify-center items-end h-[70px] fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-sm border-t border-gray-200 transition-transform duration-300 z-40">
            <div class="container max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between py-4">
                    <!-- Progress Bar -->
                    <div class="w-full flex gap-1">
                        <!-- Adım 1 -->
                        <div class="flex-1 relative">
                            <div class="h-2 bg-gray-200 rounded-l-full">
                                <div id="progress-1" class="h-full w-0 bg-green-700 rounded-l-full transition-all duration-300"></div>
                            </div>
                            <span class="hidden md:block absolute -top-6 left-0 text-xs font-medium text-gray-600">Kendi Gerçeğini Bul</span>
                        </div>
                        
                        <!-- Adım 2 -->
                        <div class="flex-1 relative">
                            <div class="h-2 bg-gray-200">
                                <div id="progress-2" class="h-full w-0 bg-green-700 transition-all duration-300"></div>
                            </div>
                            <span class="hidden md:block absolute -top-6 left-0 text-xs font-medium text-gray-600">Seçimlerin Gücü</span>
                        </div>
                        
                        <!-- Adım 3 -->
                        <div class="flex-1 relative">
                            <div class="h-2 bg-gray-200">
                                <div id="progress-3" class="h-full w-0 bg-green-700 transition-all duration-300"></div>
                            </div>
                            <span class="hidden md:block absolute -top-6 left-0 text-xs font-medium text-gray-600">Bağlantılar ve Katkı</span>
                        </div>
                        
                        <!-- Adım 4 -->
                        <div class="flex-1 relative">
                            <div class="h-2 bg-gray-200 rounded-r-full">
                                <div id="progress-4" class="h-full w-0 bg-green-700 rounded-r-full transition-all duration-300"></div>
                            </div>
                            <span class="hidden md:block absolute -top-6 left-0 text-xs font-medium text-gray-600">Anlamı Yaratmak</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include "partials/footer.php"; ?>