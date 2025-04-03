function hamburgesa() {
  const menu = document.querySelector("#myLinks ul");
  menu.classList.toggle("show");
}

// Añade esto a tu archivo home.js o crea un nuevo archivo like.js

document.addEventListener('DOMContentLoaded', function() {
  // Seleccionar todos los botones de like
  const likeButtons = document.querySelectorAll('.likeButton');
  
  // Añadir event listener a cada botón
  likeButtons.forEach(button => {
      button.addEventListener('click', handleLikeClick);
  });
  
  // Función para manejar el clic en el botón de like
  function handleLikeClick(event) {
      // Evitar comportamiento predeterminado del botón
      event.preventDefault();
      
      // Obtener el elemento que se hizo clic y el ID de la publicación
      const button = event.currentTarget;
      const postId = button.getAttribute('data-post-id');
      
      // Crear objeto FormData para enviar datos
      const formData = new FormData();
      formData.append('publicacion_id', postId);
      
      // Enviar solicitud fetch al servidor
      fetch('../php/procesarLike.php', {
          method: 'POST',
          body: formData,
          credentials: 'same-origin' // Para enviar cookies de sesión
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              // Actualizar el contador de likes
              const likeCountSpan = button.querySelector('.likeCount');
              likeCountSpan.textContent = data.likes;
              
              // Cambiar el estilo del botón según la acción
              if (data.action === 'added') {
                  button.classList.add('active');
              } else {
                  button.classList.remove('active');
              }
          } else {
              // Mostrar mensaje de error si es necesario
              console.error('Error:', data.message);
              alert(data.message);
          }
      })
      .catch(error => {
          console.error('Error en la solicitud fetch:', error);
      });
  }
});
// Añadir esto al archivo home.js existente

document.addEventListener('DOMContentLoaded', function() {
    // Manejar clic en el botón de comentarios para mostrar/ocultar la sección
    document.querySelectorAll('.commentToggle').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const commentSection = document.getElementById('comments-' + postId);
            
            if (commentSection.style.display === 'none' || !commentSection.style.display) {
                commentSection.style.display = 'block';
            } else {
                commentSection.style.display = 'none';
            }
        });
    });
    
    // Manejar envío de formularios de comentarios
    document.querySelectorAll('.commentForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const postId = this.getAttribute('data-post-id');
            const textarea = this.querySelector('textarea');
            const commentContent = textarea.value.trim();
            
            if (!commentContent) return;
            
            // Enviar comentario al servidor mediante fetch
            fetch('../php/AgregarComentario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    post_id: postId,
                    comment_content: commentContent
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Añadir el nuevo comentario a la sección de comentarios existentes
                    const existingComments = document.getElementById('existing-comments-' + postId);
                    const noCommentsMsg = existingComments.querySelector('.noComments');
                    
                    // Eliminar mensaje de "no hay comentarios" si existe
                    if (noCommentsMsg) {
                        noCommentsMsg.remove();
                    }
                    
                    // Crear y añadir el nuevo comentario
                    const newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.innerHTML = `
                        <div class="commentHeader">
                            <img src="${data.comment.profilePic}" alt="Foto de perfil" class="commentProfilePic">
                            <div>
                                <p class="commentUserName">${data.comment.username}</p>
                                <p class="commentDate">${data.comment.date}</p>
                            </div>
                        </div>
                        <p class="commentContent">${data.comment.content}</p>
                    `;
                    
                    existingComments.appendChild(newComment);
                    
                    // Limpiar el textarea
                    textarea.value = '';
                } else {
                    alert('Error al enviar el comentario: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al enviar el comentario');
            });
        });
    });
});