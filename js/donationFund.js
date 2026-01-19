const donationFund = [
  {
    image: "images/flood.jpg",
    tag: "Relief Donation",
    title: "Emergency Relief Fund",
   link: "donate.php",
    text: "Many people want to donate regularly but often forget to do so.From now on, bKash-Nagad App and Visa-Mastercard users can make regular donations from the Sidratul Muntaha Foundation website by activating the automated system. On our website, you can choose from daily, or monthly donation options and select the amount. Even if you forget, the specified amount will be credited to the foundation's account at the scheduled time.Users can opt out of this feature at any time.",
  },
  {
    image: "images/general fund.jpg",
    tag: "General Donation",
    title: "General Fund",
    link: "donate.php",
    text: "If someone donates to a specific sector, the Sidratul Muntaha Foundation spends it in that sector. The general fund is spent on every charitable activity of As- Sunnah Foundation. Also, the expenditure of different activities such as religious education of Sidratul Muntaha humanitarian services and dawah activities related total initiatives is met from the general fund.",
  },
  {
    image: "images/zakat.jpg",
    tag: "Zakat Donation",
    title: "Zakat Fund",
   link: "donate.php",
    text: "Just as Zakat is one of the fundamental pillars of Islam, it is also a humanitarian act of worship. Zakat plays the most significant role in eliminating economic inequality. Your Zakat can help move the wheels of a stagnant family.",
  },
  {
    image: "images/act4.webp",
    tag: "Regular Projects",
    title: "Meritorious Program",
   link: "donate.php",
    text: "Financial Support For Madrasha Students which are needed for poor students",
  },
  {
    image: "images/act5.webp",
    tag: "Regular Projects",
    title: "Meritorious Program",
  link: "donate.php",
    text: "Financial Support For Madrasha Students which are needed for poor students",
  },
  {
    image: "images/act6.webp",
    tag: "Regular Projects",
    title: "Meritorious Program",
  link: "donate.php",
    text: "Financial Support For Madrasha Students which are needed for poor students",
  },



];
  const container = document.getElementById("donationGrid");
 container.innerHTML = donationFund.map(donationFund => `
<a href="donate.php">
    <div class="col-lg-4 col-md-6">
      <div class="course">
        <div class="course_image"><img src="${donationFund.image}" alt=""></div>
        <div class="course_body">
          <div class="course_header d-flex flex-row align-items-center justify-content-start">
            <div class="activities_tag"><a href="#" class="text-success">${donationFund.tag}</a></div>
          
          </div>
          <div class="course_title truncated-title "><h3><a href="${donationFund.link}" >${donationFund.title}</a></h3></div>
          <div class="course_text text-truncated ">${donationFund.text}</div>
          							<div class="button button_1"><a href="donate.php">Donate Now<div class="button_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></div></a></div>
          <div class="course_footer d-flex align-items-center justify-content-start">
          </div>
        </div>
      </div>
    </div>
</a>
  `).join("");
