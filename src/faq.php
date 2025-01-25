<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            padding: 0 1.5rem;
            opacity: 0;
            transition: max-height 0.5s ease, opacity 0.5s ease, padding 0.5s ease;
        }

        .faq-answer.active {
            max-height: 200px;
            padding: 1rem 1.5rem;
            opacity: 1;
        }

        .rotate {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }

        .arrow {
            transition: transform 0.3s ease;
        }

        .faq-question {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-100">

    <section id="faq" class="text-black py-16 sm:py-16 px-2 sm:px-0">
        <div class="container mx-auto px-2 sm:px-6">
            <h2 class="text-4xl font-bold mb-8 text-center">Frequently Asked Questions</h2>
            <div class="space-y-4 mx-2 sm:mx-20">
                <?php
                $faqs = [
                    [
                        "question" => "How do I book a turf?",
                        "answer" => "You can browse available turfs on our platform, select a date and time slot, and complete your booking by making a payment online."
                    ],
                    [
                        "question" => "How do I make payments for bookings?",
                        "answer" => "Payments can be made securely through our platform using credit/debit cards, UPI, or other supported payment gateways."
                    ],
                    [
                        "question" => "Will I receive a confirmation for my booking?",
                        "answer" => "Yes, a confirmation email or SMS will be sent to you immediately after a successful booking."
                    ],
                    [
                        "question" => "What happens if the turf is unavailable due to unforeseen circumstances?",
                        "answer" => "In such cases, you will be notified immediately, and you can either reschedule or request a refund."
                    ]
                ];

                foreach ($faqs as $index => $faq) {
                    echo '
                <div class="border-b border-gray-700 opacity-0 translate-y-10" id="faq-item-' . $index . '">
                    <button class="faq-question w-full text-left py-4 px-6 flex justify-between items-center focus:outline-none" onclick="toggleAnswer(' . $index . ')">
                        <span class="text-xl sm:text-2xl font-semibold">' . $faq['question'] . '</span>
                        <span class="arrow transform transition-transform">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer" id="answer-' . $index . '">
                        <p class="py-4 px-6">' . $faq['answer'] . '</p>
                    </div>
                </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- GSAP CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script>
        gsap.registerPlugin(ScrollTrigger);

        let openIndex = null;

        function toggleAnswer(index) {
            const answer = document.getElementById('answer-' + index);
            const arrow = document.querySelectorAll('.faq-question')[index].querySelector('.arrow');

            if (openIndex === index) {
                answer.classList.remove("active");
                arrow.classList.remove("rotate");
                openIndex = null;
            } else {
                if (openIndex !== null) {
                    const previousAnswer = document.getElementById('answer-' + openIndex);
                    const previousArrow = document.querySelectorAll('.faq-question')[openIndex].querySelector('.arrow');
                    previousAnswer.classList.remove("active");
                    previousArrow.classList.remove("rotate");
                }

                answer.classList.add("active");
                arrow.classList.add("rotate");
                openIndex = index;
            }
        }

        // Animate FAQ items dynamically on scroll
        document.querySelectorAll('[id^="faq-item-"]').forEach((faqItem, index) => {
            gsap.fromTo(
                faqItem, {
                    opacity: 0,
                    y: 20,
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: faqItem,
                        start: 'top 80%',
                        end: 'bottom top',
                        toggleActions: 'play none none none',
                        scrub: true, // Enable scrub to make the animation follow the scroll
                    },
                }
            );
        });

        // Add dynamic animations for the FAQ header
        gsap.fromTo(
            'h2', {
                opacity: 0,
                y: -20,
            }, {
                opacity: 1,
                y: 0,
                duration: 1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: 'h2',
                    start: 'top 90%',
                    end: 'bottom top',
                    scrub: true, // Enable scrub to make the animation follow the scroll
                    toggleActions: 'play none none none',
                },
            }
        );
    </script>



</body>

</html>