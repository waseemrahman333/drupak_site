// menu
        let menuOptions = document.querySelector(".nav__option--links");
        window.addEventListener("resize", function(){
            if(window.innerWidth >= 1280){
                if(!menuOptions.classList.contains("hidden")){
                    menuOptions.classList.remove("hidden");
                }
            }
        })
        function openMenu(e){
            if(menuOptions.classList.contains("hidden")){
                menuOptions.classList.remove("hidden");
                e.innerHTML = `<svg class="w-[clamp(19px,3.5vw,31px)] h-[clamp(19px,3.5vw,31px)]" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 2.20941L16.7906 0L9.5 7.29059L2.20941 0L0 2.20941L7.29059 9.5L0 16.7906L2.20941 19L9.5 11.7094L16.7906 19L19 16.7906L11.7094 9.5L19 2.20941Z" fill="var(--color-tertiary)"/>
                </svg>`;
            }else{
                menuOptions.classList.add("hidden");
                e.innerHTML = `<svg class="w-[clamp(19px,3.5vw,31px)] h-[clamp(19px,3.5vw,31px)]" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="31.6667" height="3.8" fill="var(--color-tertiary)"/>
                <rect y="11.8" width="31.6667" height="3.8" fill="var(--color-tertiary)"/>
                <rect y="23.6" width="31.6667" height="3.8" fill="var(--color-tertiary)"/>
            </svg>`;
            } 
        }
        // menu dropdown
        let dropDownMenu = document.querySelector(".services");
        function dropDown(){
            if(dropDownMenu.style.maxHeight == 0 + 'px'){
                dropDownMenu.style.maxHeight = dropDownMenu.scrollHeight + 60 + 'px';
                dropDownMenu.style.opacity = 1;
                dropDownMenu.classList.add("xl:py-7.5");
            }else{
                dropDownMenu.style.maxHeight = 0 + 'px';
                dropDownMenu.style.opacity = 0;
                dropDownMenu.classList.remove("xl:py-7.5");

            }
        }
        window.addEventListener("click", (e)=>{
            if(!e.target.closest(".services, #services")){
                if(dropDownMenu.style.maxHeight != 0 + 'px'){
                    dropDownMenu.style.maxHeight = 0 + 'px';
                    dropDownMenu.style.opacity = 0;
                    dropDownMenu.classList.remove("xl:py-7.5");
                }
            }
        })


        