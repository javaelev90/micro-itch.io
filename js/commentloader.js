
// Fetch comment information
function fetchComments(projectid, min, max, commentDisplayFunction){
    var url = "https://people.dsv.su.se/~anlu5675/Webb2/ges%C3%A4llprov/getcomment.php";
    var parameters = "?projectid="+projectid+"&min="+min+"&max="+max;
    fetch(url+parameters, {method: "GET"})
        .then(function (response) {
            return response.json();
        })
        .then(function (myJson) {
            // If there are comments
            if(myJson.length){
                commentDisplayFunction(buildCommentsHTML(myJson), myJson.length);
                preventDefaultFormSubmit();
            }
        })
        .catch(function (error) {
            console.log("Error: " + error);
        });
}

// Used to show reply comment form 
function toggleReplyForm(element){
    var sibling = element.nextElementSibling;
    if(sibling.style.maxHeight == "0px" || !sibling.style.maxHeight){
        sibling.style.maxHeight = "114px";
    } else {
        sibling.style.maxHeight = "0";
    }
}

// Adds comments html onto project page and increases retrieve interval 
function loadCommentHTML(comments, numberOfComments) {
    reloadCommentHTML(comments, numberOfComments);
    increaseInterval(numberOfComments);
}

// Reloads comments html onto project page
function reloadCommentHTML(comments, numberOfComments) {
    var commentThread = document.getElementById("comment-thread");
    if(commentThread){
        commentThread.innerHTML = comments;
    }
}

// The buildCommentsHTML functions builds html based on the following json structure 
// array(
//     {
//         "id" => "1",
//         "username" => "",
//         "creationdate" => "",
//         "comment" => "",
//         "replies" => array({
//                 "id" => "2",
//                 "rootid" => "1",
//                 "parentid" => "1",
//                 "username" => "",
//                 "creationdate" => "",
//                 "comment" => ""
//             },
//             {
//                 "id" => "3",
//                 "rootid" => "1",
//                 "parentid" => "1",
//                 "username" => "",
//                 "creationdate" => "",
//                 "comment" => ""
//             }
//         )
//     },
// )
function buildCommentsHTML(commentsJson){
    var comments = "";
    for (const key in commentsJson){
        if(commentsJson.hasOwnProperty(key)){
            var commentData = commentsJson[key];
            var comment = getCommentHTML(commentData["username"], commentData["creationdate"], commentData["comment"], commentData["id"], projectid);
            
            if(commentData["replies"].length > 0){
                comment += '<div class="replies">';
                
                for(const key in commentData["replies"]){
                    var reply = commentData["replies"][key];
                    comment += getCommentHTML(reply["username"], reply["creationdate"], reply["comment"], reply["id"], projectid);
                }
                comment += "</div>";
            }
            comment += "</div>";
            comments += comment;
        }
    }
    return comments;
}

// Returns html for one comment
function getCommentHTML(username, creationdate, comment, parentid, projectid){
    return `<div class="comment">
                <div class="commenter-info">
                    <div class="comment-user">`+ username +`</div>
                    <div class="comment-date">`+ creationdate +`</div>
                </div>
                <span class="comment-text">`+ comment +`</span>
                <div class="comment-reply-btn" onclick="toggleReplyForm(this);">REPLY</div>
                <div class="reply-form">
                    <form class="comment-form" method="POST" onsubmit="sendForm(this, 'reply')">
                        <input type="hidden" name="parentid" value="`+ parentid +`"/>
                        <input type="hidden" name="projectid" value="`+ projectid +`"/>
                        <textarea required class="comment-write-area" maxlength="1000" style="resize: none;"
                        rows="4" cols="40" name="comment" placeholder="Required">@`+ username +` </textarea>
                        <input class="cbtn comment-btn" type="submit" name="post-comment" value="Post reply"/>
                        <button class="cbtn comment-btn" style="margin-right: 2px;" type="button" onclick="cancelReply(this);">Cancel</button>
                    </form>
                </div>
            </div>`;
}

// Send comment data form
function sendForm(form, formType){

    fetch("https://people.dsv.su.se/~anlu5675/Webb2/ges%C3%A4llprov/savecomment.php", {
        method: 'POST',
        body: new FormData(form)
    })
    .then(result => {
        form.comment.value = "";
        if(result["url"] && result["url"] != window.location.href){
            window.location.replace(result["url"]);
        }
        reloadComments();
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Finds comment-reply-btn in comment and triggers display transition
function cancelReply(element){
    var closestComment = element.closest(".comment");
    var displayReplyFormElement = closestComment.querySelector(".comment-reply-btn");
    toggleReplyForm(displayReplyFormElement);
}

// Load more comments onclick function
function loadMoreComments(){
    fetchComments(projectid, min, max, loadCommentHTML);
}

// Reload the first up to max comments
function reloadComments(){
    fetchComments(projectid, 0, max, reloadCommentHTML);
}

// Increase the load comment interval
function increaseInterval(loaded){
    max += (loaded == (max - min)) ? 10 : 0;
}
// This blocks the default page reload when submitting a form
function preventDefaultFormSubmit(){
    document.querySelectorAll(".comment-form").forEach(form => 
        form.addEventListener("submit", e => e.preventDefault())
    );
}

// Get current projectid
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const projectid = urlParams.get("projectid");
var min = 0;
var max = 10;

// Do some work after page has loaded
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById("hidden-projectid").value = projectid;
    preventDefaultFormSubmit();
});

// Fetch comments on page load
fetchComments(projectid, min, max, loadCommentHTML);

