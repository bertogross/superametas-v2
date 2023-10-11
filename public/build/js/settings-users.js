
//Fiter Js
var list = document.querySelectorAll(".team-list");
if (list) {
    var buttonGroups = document.querySelectorAll('.filter-button');
    if (buttonGroups) {
        Array.from(buttonGroups).forEach(function (btnGroup) {
            btnGroup.addEventListener('click', onButtonGroupClick);
        });
    }
}

function onButtonGroupClick(event) {
    if (event.target.id === 'list-view-button' || event.target.parentElement.id === 'list-view-button') {
        document.getElementById("list-view-button").classList.add("active");
        document.getElementById("grid-view-button").classList.remove("active");
        Array.from(list).forEach(function (el) {
            el.classList.add("list-view-filter");
            el.classList.remove("grid-view-filter");
        });

    } else {
        document.getElementById("grid-view-button").classList.add("active");
        document.getElementById("list-view-button").classList.remove("active");
        Array.from(list).forEach(function (el) {
            el.classList.remove("list-view-filter");
            el.classList.add("grid-view-filter");
        });
    }
}

var editlist = false;

// avatar image
document.querySelector("#member-image-input").addEventListener("change", function () {
    var preview = document.querySelector("#member-img");
    var file = document.querySelector("#member-image-input").files[0];
    var reader = new FileReader();
    reader.addEventListener("load",function () {
        preview.src = reader.result;
    },false);
    if (file) {
        reader.readAsDataURL(file);
    }
});


// cover image
document.querySelector("#cover-image-input").addEventListener("change", function () {
    var preview = document.querySelector("#cover-img");
    var file = document.querySelector("#cover-image-input").files[0];
    var reader = new FileReader();
    reader.addEventListener("load",function () {
        preview.src = reader.result;
    },false);
    if (file) {
        reader.readAsDataURL(file);
    }
});

function editMemberList() {
    var getEditid = 0;
    Array.from(document.querySelectorAll(".edit-list")).forEach(function (elem) {
        elem.addEventListener('click', function (event) {
            getEditid = elem.getAttribute('data-edit-id');
            allmemberlist = allmemberlist.map(function (item) {
                if (item.id == getEditid) {
                    editlist = true;
                    document.getElementById("createMemberLabel").innerHTML = "Edit Member";
                    document.getElementById("addNewMember").innerHTML = "Save";

                    if(item.memberImg == ""){
                        document.getElementById("member-img").src = "build/images/users/user-dummy-img.jpg";
                    }else{
                        document.getElementById("member-img").src = item.memberImg;
                    }

                    document.getElementById("cover-img").src = item.coverImg;
                    document.getElementById("memberid-input").value = item.id;
                    document.getElementById('teammembersName').value = item.memberName;
                    document.getElementById('designation').value = item.position;
                    document.getElementById('project-input').value = item.projects;
                    document.getElementById('task-input').value = item.tasks;
                    document.getElementById("memberlist-form").classList.remove('was-validated');
                }
                return item;
            });
        });
    });
};


Array.from(document.querySelectorAll(".addMembers-modal")).forEach(function (elem) {
    elem.addEventListener('click', function (event) {
      document.getElementById("createMemberLabel").innerHTML = "Adicionar";
      document.getElementById("addNewMember").innerHTML = "Add Member";
      document.getElementById("teammembersName").value = "";
      document.getElementById("designation").value = "";

      document.getElementById("cover-img").src = "build/images/small/img-9.jpg";
      document.getElementById("member-img").src = "build/images/users/user-dummy-img.jpg";

      document.getElementById("memberlist-form").classList.remove('was-validated');
    });
});

// Form Event
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    event.preventDefault();
                    var inputName = document.getElementById('teammembersName').value;
                    var inputDesignation = document.getElementById('designation').value;
                    var memberImg = document.getElementById("member-img").src;
                    var coverImg = document.getElementById("cover-img").src;

                    var memberImgValue = memberImg.substring(
                        memberImg.indexOf("/as") + 1
                    );

                    var memberImageValue
                    if(memberImgValue == "build/images/users/user-dummy-img.jpg"){
                        memberImageValue = ""
                    }else{
                        memberImageValue = memberImg
                    }

                    var str = inputName;
                    var matches = str.match(/\b(\w)/g);
                    var acronym = matches.join(''); // JSON
                    var nicknameValue = acronym.substring(0,2)

                    if (inputName !== "" && inputDesignation !== "" && !editlist) {
                        var newMemberId = findNextId();
                        var newMember = {
                            'id': newMemberId,
                            "coverImg": coverImg,
                            "bookmark": false,
                            "memberImg": memberImageValue,
                            "nickname": nicknameValue,
                            'memberName': inputName,
                            'position': inputDesignation,
                            'projects': "0",
                            'tasks': "0"
                        };

                        allmemberlist.push(newMember);

                        sortElementsById();

                    }else if(inputName !== "" && inputDesignation !== "" && editlist){
                        var getEditid = 0;
                        getEditid = document.getElementById("memberid-input").value;
                        allmemberlist = allmemberlist.map(function (item) {
                            if (item.id == getEditid) {
                                var editObj = {
                                    'id': getEditid,
                                    "coverImg": coverImg,
                                    "bookmark": item.bookmark,
                                    "memberImg": memberImg,
                                    "nickname": nicknameValue,
                                    'memberName': inputName,
                                    'position': inputDesignation,
                                    'projects': document.getElementById('project-input').value,
                                    'tasks': document.getElementById('task-input').value
                                }
                                return editObj;
                            }
                            return item;
                        });
                        editlist = false;
                    }

                    loadTeamData(allmemberlist)
                    document.getElementById("createMemberBtn-close").click();
                }
                form.classList.add('was-validated');
            }, false)
        })
})()



function removeItem() {
    var getid = 0;
    Array.from(document.querySelectorAll(".remove-list")).forEach(function (item) {
        item.addEventListener('click', function (event) {
            getid = item.getAttribute('data-remove-id');
            document.getElementById("remove-item").addEventListener("click", function () {
                function arrayRemove(arr, value) {
                    return arr.filter(function (ele) {
                        return ele.id != value;
                    });
                }
                var filtered = arrayRemove(allmemberlist, getid);

                allmemberlist = filtered;

                loadTeamData(allmemberlist);
                document.getElementById("close-removeMemberModal").click();
            });
        });
    });
}

function memberDetailShow() {
    Array.from(document.querySelectorAll(".team-box")).forEach(function (item) {
        item.querySelector(".member-name").addEventListener("click", function () {

            var memberName = item.querySelector(".member-name h5").innerHTML;
            var memberDesignation = item.querySelector(".member-designation").innerHTML;

            var memberProfileImg
            if(item.querySelector(".member-img")){
                memberProfileImg = item.querySelector(".member-img").src;
            }else{
                memberProfileImg = "build/images/users/user-dummy-img.jpg"
            }
            var memberCoverImg = item.querySelector(".team-cover img").src;
            var memberProject = item.querySelector(".projects-num").innerHTML;
            var memberTask = item.querySelector(".tasks-num").innerHTML;

            document.querySelector("#member-overview .profile-img").src = memberProfileImg;
            document.querySelector("#member-overview .team-cover img").src = memberCoverImg;

            document.querySelector("#member-overview .profile-name").innerHTML = memberName;
            document.querySelector("#member-overview .profile-designation").innerHTML = memberDesignation;

            document.querySelector("#member-overview .profile-project").innerHTML = memberProject;
            document.querySelector("#member-overview .profile-task").innerHTML = memberTask;
        });
    });
}

// Search member on list
var allmemberlist = '';
var searchMemberList = document.getElementById("searchMemberList");
searchMemberList.addEventListener("keyup", function () {
    var inputVal = searchMemberList.value.toLowerCase();
    function filterItems(arr, query) {
        return arr.filter(function (el) {
            return (el.memberName.toLowerCase().indexOf(query.toLowerCase()) !== -1 || el.position.toLowerCase().indexOf(query.toLowerCase()) !== -1)
        })
    }

    var filterData = filterItems(allmemberlist, inputVal);
    if (filterData.length == 0) {
        document.getElementById("noresult").style.display = "block";
        document.getElementById("teamlist").style.display = "none";
    } else {
        document.getElementById("noresult").style.display = "none";
        document.getElementById("teamlist").style.display = "block";
    }

    loadTeamData(filterData);
});
