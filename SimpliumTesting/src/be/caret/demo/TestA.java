package be.caret.demo;

import java.awt.Robot;
import java.awt.event.KeyEvent;
import java.awt.image.*;
import java.io.File;
import java.io.IOException;

import javax.imageio.ImageIO;

import junit.framework.Assert;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.simplium.SimpliumWebTest;
import org.simplium.junit.SimpliumJUnitRunner;

@CaretWebTest
@RunWith(SimpliumJUnitRunner.class)
public class TestA extends SimpliumWebTest {

        String path = "";
        int refRed = 255;
        int refGreen = 255;
        int refBlue = 255;
        int x = 20;
        int y = 150;
        int topPosX,topPosY,bottomPosX,bottomPosY;           //the values of the top left corner
        String browser = "";
        String os = "";
        int colors[] = new int[3];

        public void setConfiguration() throws Exception{            
            //Get the browser viewport config
            selenium.windowMaximize();
            selenium.windowFocus();
            
            
            
            //Focus on screen, check screenshot taking
            
            
            selenium.getEval("window.moveTo(1,0);");
            getBrowserInformation();
            String configPath = "/Users/hh354/Documents/screenshots/config/"+ browser + "/screenshot.png";
            selenium.captureScreenshot(configPath);

            //find the viewport
            File file= new File(configPath);
            BufferedImage image = ImageIO.read(file);
            int clr;
            try{
                //first check the left side
                do{
                    if(x > 0){
                        // Getting pixel color by position x=20 and y=150
                        clr = image.getRGB(x,y);
                        colors = findRGB(clr);
                        x--;
                    }else{
                        break;
                    }
                }while(colors[0] == 255 && colors[1] == 255 && colors[2] == 255);
                topPosX = x++;
                
                //then check the top side
                do{
                    if(y > 0){
                        // Getting pixel color by position
                        clr = image.getRGB(x,y);
                        colors = findRGB(clr);
                        y--;
                    }else{
                        break;
                    }
                }while(colors[0] == 255 && colors[1] == 255 && colors[2] == 255);
                topPosY = y++;
                
                //check the right side
                x = topPosX + Integer.parseInt(selenium.getEval("document.body.clientWidth"));
                y = topPosY + Integer.parseInt(selenium.getEval("document.body.clientHeight"));
                do{
                    if(x < image.getWidth() - 1){
                        // Getting pixel color by position
                        clr = image.getRGB(x,y);
                        colors = findRGB(clr);
                        x++;
                    }else{
                        break;
                    }
                }while(colors[0] == 255 && colors[1] == 255 && colors[2] == 255);
                bottomPosX = x--;
                
                //then check the bottom side
                do{
                    if(y < image.getHeight() - 1){
                        // Getting pixel color by position
                        clr = image.getRGB(x,y);
                        colors = findRGB(clr);
                        y++;
                    }else{
                        break;
                    }
                }while(colors[0] == 255 && colors[1] == 255 && colors[2] == 255);
                bottomPosY = y--;
                
                System.out.println("Position Top X: " + topPosX);
                System.out.println("Position Top Y: " + topPosY);
                System.out.println("Position Bottom X: " + bottomPosX);
                System.out.println("Position Bottom Y: " + bottomPosY);
            }catch(Exception ex){
                System.out.println(ex);
            }
        }
        
        private int[] findRGB(int clr){
            //calculating corresponding rgb values
            colors = new int[3];
            colors[0]   = (clr & 0x00ff0000) >> 16;
            colors[1] = (clr & 0x0000ff00) >> 8;
            colors[2]  =  clr & 0x000000ff;
            return colors;
        }

        @Test
        public void testUntitled() throws Exception {

            setConfiguration();
            ////////////////////////////////////
            // Browser and Test Configuration //
            ////////////////////////////////////

            //This could select the window but first we need to test without eclipse on.
            //String test = selenium.getAllWindowTitles()[0];
            //selenium.selectWindow(test);

            getBrowserInformation();

            //complete path to save screenshot to
            path = "/Users/hh354/Documents/screenshots/"+ os + "/" + browser + "/";


            ////////////////////
            // Start the test //
            ////////////////////

            selenium.open("/");
            selenium.click("//div[@id='node-2']/div/div/div[2]/h4[1]/a");
            selenium.waitForPageToLoad("30000");
            selenium.click("link=Product Features");
            selenium.waitForPageToLoad("30000");
            selenium.type("edit-search-theme-form-1", "cambridge");
            selenium.click("edit-submit");
            selenium.waitForPageToLoad("30000");
            //This line has to be converted by ourselves: verifyTrue(selenium.isTextPresent("Show:"));
            Assert.assertTrue(selenium.isTextPresent("Search results"));
     
            int i = 0;
            //capture a screenshot of the webpage
            //the testid will be added to the filename later on
            selenium.captureScreenshot(path + "screenshot" + i + ".png");
            //crop it
            cropImage(i);

            //take screenshots of the full page and compare each time with the previous screenshot
            do{
                i++;
                //Page down
                selenium.getEval("window.scrollTo(0,500);");
                Thread.sleep(500);
                //Take new screenshot
                selenium.captureScreenshot(path + "screenshot" + i + ".png");
                //crop it
                cropImage(i);
            }while(!checkWithPreviousShot(i));

            //Get all the screenshots
            //File[] files = NaiveSimilarityFinder.getOtherImageFiles(new File(path + "screenshot" + i + ".png"));
            
            //Merge the screenshots
            //Loop over the images with a box of 15x100 and compare them to find similar parts

        }

        private void cropImage(int id){
            File f = new File(path + "screenshot" + id + ".png");
            if(f.exists()){
                try {
                    //crop the screenshot
                    BufferedImage img = ImageIO.read(f).getSubimage(topPosX, topPosY, bottomPosX - topPosX, bottomPosY - topPosY);
                    //make sure the 'cropped' dir has been created
                    if(!new File(path + "cropped/").exists()){
                        new File(path + "cropped/").mkdir();
                    }

                    //Create a file
                    File outputfile = new File(path + "cropped/" + f.getName());
                    //Save the image
                    ImageIO.write(img, "png", outputfile);

                } catch (IOException e) {
                    System.out.println(e);
                }

            }
        }

        private void getBrowserInformation(){
            //get the browser
            //get the complete client info with some JS and split it
            String[] clientinfo = selenium.getEval("navigator.userAgent;").split(" ");
            //get only the browser info at the end of the array
            browser = clientinfo[clientinfo.length - 1];
            //remove the version number
            browser = browser.split("/")[0];

            //get the OS
            //get the complete client info with some JS and split it
            clientinfo = selenium.getEval("navigator.userAgent;").split(";");
            //get the OS information and replace spaces with underscores
            os = clientinfo[2].replace(" ","_");
            //replace dots by underscores
            os = os.replace(".","_");
        }
        private boolean checkWithPreviousShot(int id){
            //Check if the screenshots exist
            File previousScreenshot = new File(path + "cropped/screenshot" + (id-1) + ".png");
            File lastScreenshot = new File(path + "cropped/screenshot" + id + ".png");
            //Check if the screenshot is the same as the previous
            try{
                double distance = NaiveSimilarityFinder.TestTwoImages(previousScreenshot, lastScreenshot);
                if(distance < 5){
                    //delete last screenshot because there was no scrolling
                    lastScreenshot.delete();
                    return true;
                }else{
                    return false;
                }
            }catch(IOException ex){
                System.out.println(ex);
                return false;
            }
        }
}