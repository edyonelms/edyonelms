   <div>
       {{-- Navigation Bar --}}
       @livewire('website.nav')

       {{-- Hero Section --}}
       @livewire('website.hero-section')

       @livewire('website.school-slider')

       {{-- About Us --}}
       @livewire('website.about-us')

       {{-- Feautures Section --}}
       @livewire('website.features')

       <!-- Social Media Sidebar -->
       <div class="fixed left-0 top-1/3 z-50">
           <div
               class="flex flex-col space-y-3 bg-white p-4 rounded-r-lg shadow-md border-l-4 border-pink-500 shadow-pink-glow">

               <a href="https://www.instagram.com/edyonelms?utm_source=ig_web_button_share_sheet&igsh=ZzlkamE5ZTR5MDB6"
                   target="_blank" rel="noopener noreferrer">
                   <img src="https://img.icons8.com/fluency/48/instagram-new.png" alt="Instagram" class="w-8 h-8" />
               </a>

               <a href="https://whatsapp.com/channel/0029Vb6myRCKGGGKSCgVFP0M" target="_blank" rel="noopener noreferrer">
                   <img src="https://img.icons8.com/color/48/whatsapp--v1.png" alt="WhatsApp" class="w-8 h-8" />
               </a>

               <a href="https://youtube.com/@edyonelms?si=SqStdSrTtJ95j8tP" target="_blank" rel="noopener noreferrer">
                   <img src="https://img.icons8.com/color/48/youtube-play.png" alt="YouTube" class="w-8 h-8" />
               </a>

           </div>
       </div>

       {{-- Terms and Conditon --}}
       @livewire('website.terms-condition')
       <!-- Contact Us section -->
       @livewire('website.contact-us')

       <section x-data="testimonialSlider()" x-init="init()"
           class="relative py-20 bg-gradient-to-br from-white to-purple-50 overflow-hidden">
           <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center relative z-10">
               <!-- Left Content -->
               <div>
                   <p class="text-sm font-semibold text-indigo-600 mb-2">Testimonials</p>
                   <h2 class="text-4xl font-extrabold text-gray-900 mb-4 leading-snug">
                       Why Our Learners<br />
                       <span class="text-indigo-600">‘Appreciate’</span> Us
                   </h2>
                   <p class="text-gray-600 text-base leading-relaxed">
                       With features like easy course access, interactive content, real-time progress tracking, and
                       anytime-anywhere availability, learners feel supported and motivated throughout their educational
                       journey.
                   </p>
               </div>

               <!-- Right: Vertical Scroll Container -->
               <div class="relative h-[280px] overflow-y-auto scrollbar-hide" x-ref="container">
                   <div class="transition-all duration-700 ease-in-out space-y-6" x-ref="track">
                       <template x-for="(item, index) in visibleTestimonials" :key="index">
                           <div class="testimonial bg-white rounded-xl p-6 shadow-md border border-gray-200">
                               <h3 class="text-xl font-bold text-black mb-2" x-text="item.feedbackTitle"></h3>
                               <p class="text-gray-600 mb-4" x-text="item.feedback"></p>
                               <div class="flex items-center gap-4">
                                   {{-- <img :src="item.image" class="w-10 h-10 rounded-full object-cover" /> --}}
                                   <div>
                                       <p class="font-semibold text-black" x-text="item.name"></p>
                                       <p class="text-sm text-gray-500" x-text="item.role"></p>
                                   </div>
                               </div>
                               <div class="mt-2 text-yellow-400 text-sm" x-html="getStars(item.rating)"></div>
                           </div>
                       </template>
                   </div>
               </div>
           </div>

           <!-- Decorative Glow -->
           <div
               class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-purple-100 rounded-full blur-[120px] opacity-50 z-0">
           </div>
       </section>

       <!-- CSS: Hide Scrollbar -->
       <style>
           .scrollbar-hide {
               scrollbar-width: none;
               -ms-overflow-style: none;
           }

           .scrollbar-hide::-webkit-scrollbar {
               display: none;
           }
       </style>

       <!-- Alpine.js Logic -->
       <script>
           function testimonialSlider() {
               return {
                   testimonials: [{
                           name: "Ritika Sharma",
                           role: "Principal, Delhi Public Academy",
                           image: "https://randomuser.me/api/portraits/women/44.jpg",
                           rating: 5,
                           feedbackTitle: "⭐⭐⭐⭐⭐",
                           feedback: `“Edyone LMS has transformed the way our school functions. From fee management to report cards, everything is now streamlined and paperless. The seating plan and admit card generation during exams saved us a lot of manual effort. A truly efficient solution for schools.”`
                       },
                       {
                           name: "Amit Verma",
                           role: "Math Teacher, Patna Model School",
                           image: "https://randomuser.me/api/portraits/men/45.jpg",
                           rating: 4,
                           feedbackTitle: "⭐⭐⭐⭐☆",
                           feedback: `“The platform is very teacher-friendly. I can easily upload homework, study materials, and conduct quizzes without needing extra training. The performance analytics also help me understand which students need extra support. I just wish the app loaded a bit faster sometimes.”`
                       },
                       {
                           name: "Sneha Iyer",
                           role: "Parent, Mumbai",
                           image: "https://randomuser.me/api/portraits/women/33.jpg",
                           rating: 5,
                           feedbackTitle: "⭐⭐⭐⭐⭐",
                           feedback: `“As a parent, I really appreciate the transparency Edyone LMS provides. I can track my child’s attendance, see homework updates, and even view the syllabus and announcements from the school. The ID card and timetable features are very useful too.”`
                       },
                       {
                           name: "Rahul Mehta",
                           role: "IT Coordinator, Modern Public School, Jaipur",
                           image: "https://randomuser.me/api/portraits/men/36.jpg",
                           rating: 5,
                           feedbackTitle: "⭐⭐⭐⭐⭐",
                           feedback: `“The best part of Edyone LMS is its customizability. We were able to adapt it to our school’s academic structure easily. Integration was smooth, and their support team is quick to respond. Looking forward to more updates and advanced analytics.”`
                       },
                       {
                           name: "Kavita Deshmukh",
                           role: "Science Teacher, Pune International School",
                           image: "https://randomuser.me/api/portraits/women/41.jpg",
                           rating: 4,
                           feedbackTitle: "⭐⭐⭐⭐☆",
                           feedback: `“The arrangement feature is a blessing! Whenever a teacher is absent, it quickly updates the timetable with substitutes. The ability to share study content and track quiz scores has made teaching much more engaging and data-driven.”`
                       },
                       {
                           name: "Mohammed Arif",
                           role: "Vice Principal, Al-Falah School, Hyderabad",
                           image: "https://randomuser.me/api/portraits/men/52.jpg",
                           rating: 5,
                           feedbackTitle: "⭐⭐⭐⭐⭐",
                           feedback: `“Edyone LMS covers everything—from syllabus planning and rule management to report cards and library management. It's an all-in-one solution that every school should consider. The mobile app experience is decent, though occasional updates could make it even smoother.”`
                       }
                   ],
                   visibleTestimonials: [],
                   scrollIndex: 0,
                   scrollEvery: 5000,
                   container: null,
                   track: null,

                   init() {
                       this.visibleTestimonials = [...this.testimonials, ...this.testimonials]; // clone for infinite loop
                       this.container = this.$refs.container;
                       this.track = this.$refs.track;

                       setInterval(() => this.autoScroll(), this.scrollEvery);
                   },

                   autoScroll() {
                       const children = this.track.children;
                       if (this.scrollIndex + 1 >= children.length) return;

                       const next = children[this.scrollIndex + 1];
                       this.container.scrollTo({
                           top: next.offsetTop,
                           behavior: 'smooth'
                       });

                       this.scrollIndex++;

                       if (this.scrollIndex >= this.testimonials.length) {
                           setTimeout(() => {
                               this.container.scrollTo({
                                   top: 0
                               });
                               this.scrollIndex = 0;
                           }, 700);
                       }
                   },

                   getStars(rating) {
                       const full = '★'.repeat(rating);
                       const empty = '☆'.repeat(5 - rating);
                       return `<span>${full}${empty}</span>`;
                   }
               }
           }
       </script>


       <!-- FAQ Section -->
       <section class="relative bg-white py-20 px-4" x-data="faqSection()">

           <div
               class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[90%] h-[400px] bg-pink-100 rounded-full blur-3xl opacity-50 z-0">
           </div>

           <!-- FAQ Card Content -->
           <div class="max-w-5xl mx-auto relative z-10">
               <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8">

                   <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">Frequently Asked Questions</h2>

                   <!-- FAQ List -->
                   <template x-for="(faq, index) in faqs" :key="index">
                       <div class="mb-4 border-b border-gray-200 pb-4">
                           <div class="flex justify-between items-center cursor-pointer"
                               @click="open === index ? open = null : open = index">
                               <p class="font-semibold text-gray-800 text-base md:text-lg" x-text="faq.question"></p>
                               <button
                                   class="w-6 h-6 flex items-center justify-center rounded-full bg-blue-500 text-white transition-transform duration-300"
                                   :class="{ 'rotate-180': open === index }">
                                   <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                       viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                   </svg>
                               </button>
                           </div>
                           <div x-show="open === index" x-collapse
                               class="mt-3 text-gray-600 text-sm md:text-base leading-relaxed" x-text="faq.answer">
                           </div>
                       </div>
                   </template>

               </div>
           </div>
       </section>

       <script>
           function faqSection() {
               return {
                   open: null,
                   faqs: [{
                           question: "1. What is EDYONE LMS, and who is it designed for?",
                           answer: "Edyone LMS is a comprehensive Learning Management System built specifically for schools and educational institutions. It is designed to serve students, teachers, and administrators by offering tools for digital learning, academic management, communication, and performance tracking."
                       },
                       {
                           question: "2. How easy is it to get started with EDYONE LMS?",
                           answer: "Getting started with Edyone LMS is quick and seamless. Once your school is onboarded, each user receives login credentials and guided support to begin using the platform. The user-friendly interface ensures that even first-time users can navigate it effortlessly."
                       },
                       {
                           question: "3. Can EDYONE LMS be accessed on mobile devices?",
                           answer: "Yes, Edyone LMS is fully mobile-compatible. Students, teachers, and parents can access all features via smartphones and tablets, making learning and school management accessible anytime, anywhere."
                       },
                       {
                           question: "4. Does EDYONE LMS support attendance and fee management?",
                           answer: "Absolutely. Edyone LMS includes powerful modules for real-time attendance tracking and a secure, transparent fee management system that simplifies payments, receipts, and record-keeping."
                       },
                       {
                           question: "5. Is EDYONE LMS customizable to suit specific institutional needs?",
                           answer: "Yes, Edyone LMS is highly customizable. Schools can tailor modules, permissions, design layouts, and workflows according to their academic structure and operational requirements."
                       },
                       {
                           question: "6. What security measures does EDYONE LMS have in place?",
                           answer: "Edyone LMS uses encrypted user authentication, secure cloud hosting, and role-based access control to ensure data privacy and system security. Regular backups and compliance with data protection standards further strengthen platform safety."
                       },
                       {
                           question: "7. Can teachers easily share study materials and notes through EDYONE LMS?",
                           answer: "Yes, teachers can upload and share documents, presentations, videos, and notes with students directly through the platform. These materials can be accessed by students anytime for continued learning and revision."
                       }
                   ]
               };
           }
       </script>

       <style>
           .shadow-pink-glow {
               box-shadow: -4px 0 15px rgba(236, 72, 153, 0.5);
           }
       </style>


       {{-- Footer --}}
       @livewire('website.footer')
   </div>
