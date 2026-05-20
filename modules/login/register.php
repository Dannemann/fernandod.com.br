<?php
/**
 * Register.php
 *
 * Displays the registration form if the user needs to sign-up,
 * or lets the user know, if he's already logged in, that he
 * can't register another name.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 19, 2004
 */
require_once '../../php/inc/autoload2.php';

include 'include/session.php';
?>

<html>
<title>FKD: Cadastro de usu&aacute;rio</title>
<body>

<?php
/**
 * The user is already logged in, not allowed to register.
 */
if($session->logged_in){
   echo "<h1>Usu&aacute;rio j&aacute; registrado!</h1>";
   echo "<p>Desculpe-nos <b>$session->username</b>. Você j&aacute; est&aacute; registrado."
	   ."<a href=\"main.php\">Principal</a>.</p>";
}
/**
 * The user has submitted the registration form and the
 * results have been processed.
 */
else if(isset($_SESSION['regsuccess'])){
   /* Registration was successful */
   if($_SESSION['regsuccess']){
	  echo "<h1>Sucesso!</h1>";
	  echo "<p>Obrigado <b>".$_SESSION['reguname']."</b>, seus dados foram adicionados ao sistema. "
		  ."Voc&ecirc; pode <i>logar</i> no sistema agora.</p>";
   }
   /* Registration failed */
   else{
	  echo "<h1>Erro ao registrar usu&aacute;rio</h1>";
	  echo "<p>Desculpe-nos mas um erro ocorreu ao registrar o usu&aacute;rio <b>".$_SESSION['reguname']."</b>"
		  .".<br>Por favor tente novamente. Se o erro persistir tente novamente mais tarde.</p>";
   }
   unset($_SESSION['regsuccess']);
   unset($_SESSION['reguname']);
}
/**
 * The user has not filled out the registration form yet.
 * Below is the page with the sign-up form, the names
 * of the input fields are important and should not
 * be changed.
 */
else{
?>

<h1>Cadastrar novo usu&aacute;rio</h1>
<?php
if($form->num_errors > 0){
   echo "<td><font size=\"2\" color=\"#ff0000\">".$form->num_errors." erro(s) encontrado(s)</font></td>";
}
?>
<form action="process.php" method="POST">
<table align="left" border="0" cellspacing="0" cellpadding="3">
<tr><td>Usu&aacute;rio:</td><td><input type="text" name="user" maxlength="30" value="<?php echo $form->value("user"); ?>"></td><td><?php echo $form->error("user"); ?></td></tr>
<tr><td>Senha:</td><td><input type="password" name="pass" maxlength="30" value="<?php echo $form->value("pass"); ?>"></td><td><?php echo $form->error("pass"); ?></td></tr>
<tr><td>E-Mail:</td><td><input type="text" name="email" maxlength="50" value="<?php echo $form->value("email"); ?>"></td><td><?php echo $form->error("email"); ?></td></tr>
<tr><td colspan="2" align="right">
<input type="hidden" name="subjoin" value="1">
<input type="submit" value="Pronto!"></td></tr>
<!--<tr><td colspan="2" align="left"><a href="main.php">Voltar</a></td></tr>-->
</table>
</form>

<?php
}
?>

</body>
</html>
