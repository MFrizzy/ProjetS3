<?php
/**
 * Created by PhpStorm.
 * User: tangu
 * Date: 26/11/2017
 * Time: 22:55
 */

require_once File::build_path(array('model','Model.php'));

class ModelOrder
{

    protected static $object = 'Orders';
    protected static $primary = 'idOrder';

    private $idOrder;
    private $idUser;
    private $date;
    private $heure;
    private $adresseLivraison;
    private $products;

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    } // C le tableau du parametres products du panier lol ez pz

    public function __construct($idOrder, $idUser, $date, $heure, $adresseLivraison, $products)
    {
        $this->$idOrder = $idOrder;
        $this->$idUser = $idUser;
        $this->$date = $date;
        $this->$heure = $heure;
        $this->$adresseLivraison = $adresseLivraison;
        $this->$products = $products;
    }

    public static function selectAll()
    {
        try
        {
            $sql='SELECT * FROM Orders ';
            $rep=Model::$pdo->prepare($sql);
            $rep->setFetchMode(PDO::FETCH_CLASS,'ModelOrder');
            return $rep->fetchAll();
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public static function select($idOrder)
    {
        try
        {
            $sql = 'SELECT * FROM Orders WHERE idOrder=:idOrder';
            $rep = Model::$pdo->prepare($sql);
            $values = array("idOrder" => $idOrder);
            $rep->execute($values);
            $rep->setFetchModel(PDO::FETCH_CLASS,'ModelOrder');
            $retourne=$rep->fetchAll();
            if(empty($retourne)) {return false;}
            $retourne=$retourne[0];
            // TODO : Partie 2 : remplir le tableau $products de $retourne avec les produits de la commande
            $sql = 'SELECT idProduct,quantity FROM ProductsOrders WHERE idOrder=:idOrder';
            $rep = Model::$pdo->prepare($sql);
            $rep->execute($values);
            $retourne->setProducts($rep->fetchAll(PDO::FETCH_OBJ));
            return $retourne;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public static function delete($idOrder)
    {
        try {
            $sql = 'DELETE FROM Orders WHERE idOrder=:idOrder';
            $rep_query = Model::$pdo->prepare($sql);
            $values = array(
                "idOrder" => $idOrder
            );
            $rep_query->execute($values);
        } catch (Exception $ex) {
            return false;
        }
    }

    public static function selectByUser($idUser) {
        try {
            $sql = 'SELECT idProduct, quantity FROM ProductsOrders po JOIN Orders o ON o.idOrder = po.idOrder WHERE o.idUser=:idUser';
            $rep_query = Model::$pdo->prepare($sql);
            $values = array(
                "idUser" => $idUser
            );
            $rep_query->execute($values);
            $rep_query->setFetchModel(PDO::FETCH_CLASS, 'ModelOrder');
            $tab_order = $rep_query->fetchAll();
            return $tab_order;
        } catch (Exception $ex) {
            return false;
        }
        
    }
    
    public static function save()
    {
        //TODO WES
        try {
            if(ModelUser::getActivated() == true) {
                $sql = 'INSERT INTO Orders (idOrder, idUser, date, heure, adresseLivraison, products) VALUES (:idOrder, :idUser, :date, :heure, :adresseLivraison, :products)';
                $rep_query = Model::$pdo->prepare($sql);
                $values = array(
                    "idOrder" => $this->idOrder,
                    "idUser" => $this->idUser,
                    "date" => $this->date,
                    "heure" => $this->heure,
                    "adresseLivraison" => $this->adresseLivraison,
                    "products" => $this->products
                );
                $rep_query->execute($values);
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

}